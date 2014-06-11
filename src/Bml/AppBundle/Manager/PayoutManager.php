<?php
/**
 * Created by PhpStorm.
 * User: bml
 * Date: 02.05.14
 * Time: 15:02
 */

namespace Bml\AppBundle\Manager;


use Bml\AppBundle\Entity\Deposit;
use Bml\AppBundle\Entity\DepositRepository;
use Bml\AppBundle\Entity\Payout;
use Bml\AppBundle\Entity\PayoutRepository;
use Bml\AppBundle\Entity\PayoutTx;
use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\AppBundle\Entity\StatsRepository;
use Bml\CoinBundle\Exception\RequestException;
use Bml\CoinBundle\Manager\CoinManager;
use Bml\CoinBundle\Manager\CoinManagerContainer;
use Doctrine\ORM\EntityManager;

class PayoutManager
{


    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var WalletManager
     */
    private $walletManager;


    /**
     * @var PayoutRepository
     */
    private $payoutRepo;

    /**
     * @var DepositRepository
     */
    private $depositRepo;

    /**
     * @var Round
     */
    private $round;

    public function __construct(WalletManager $walletManager, EntityManager $em, PayoutRepository $payoutRepo,
                         RoundRepository $roundRep, DepositRepository $depositRepo)
    {
        $this->walletManager = $walletManager;
        $this->em = $em;
        $this->payoutRepo = $payoutRepo;
        $this->round = $roundRep->findCurrent();
        $this->depositRepo = $depositRepo;
    }


    /**
     * creates payout references for deposit
     * does not flush
     *
     * @param Deposit $deposit
     */
    public function createPayout(Deposit $deposit)
    {
        $payout = new Payout();
        $payout->setAccount($deposit->getAccount());
        $payout->setAmount(($deposit->getAmount() * $this->round->getPayoutPercent()) / 100);
        $payout->setExpectedAmount($payout->getAmount());
        $payout->setFee(($payout->getAmount() * $this->round->getPayoutFeePercent()) / 100);
        $payout->setType(Payout::TYPE_DEFAULT_PAYOUT);
        if ($deposit->isConfirmed()) {
            $payout->setReadyForPayout(true);
        }
        $payout->setDeposit($deposit);
        $this->em->persist($payout);
        $deposit->getPayouts()->add($payout);

        // referrer payout
        if ($deposit->getAccount()->getReferrer()) {
            $payout = new Payout();
            $payout->setAccount($deposit->getAccount()->getReferrer());
            $payout->setAmount(($deposit->getAmount() * $this->round->getReferrerPayoutPercent()) / 100);
            $payout->setExpectedAmount($payout->getAmount());
            $payout->setFee(($payout->getAmount() * $this->round->getPayoutFeePercent()) / 100);
            $payout->setType(Payout::TYPE_REFERRER_PAYOUT);
            if ($deposit->isConfirmed()) {
                $payout->setReadyForPayout(true);
            }
            $payout->setDeposit($deposit);
            $this->em->persist($payout);
            $deposit->getPayouts()->add($payout);
        }
    }

    /**
     * @throws \Bml\CoinBundle\Exception\RequestException
     * @throws \Exception
     * @return \Bml\AppBundle\Entity\Payout[]
     */
    public function realisePayouts()
    {
        $balance = $this->round->getStats()->getBalance();

        $payouts = $this->payoutRepo->findForPayout($this->round);

        $payoutsToRealise = [];
        /* @var $payoutsToRealise Payout[] */
        $sumPayout = 0;
        foreach ($payouts as $payout) {
            if ($payout->getDeposit()->isConfirmed()) {
                $sumPayout += $payout->getAmount() + $payout->getFee();
                if ($sumPayout > $balance) {
                    break;
                }
                $payoutsToRealise[] = $payout;
            }
        }


        $realisedPayouts = $this->_realisePayouts($payoutsToRealise);


        return $realisedPayouts;
    }

    public function realiseRoundEndPayouts()
    {
        // last deposit
        $balance = $this->round->getStats()->getBalance();
        if (!$balance) {
            return;
        }
        $lastPayoutPercent = $this->round->getLastPayoutPercent();
        $payoutsToRealise = [];
        if ($lastPayoutPercent > 0) {
            $lastDeposit = $this->depositRepo->findOneBy(['round' => $this->round->getId()], ['id' => 'desc']);

            $payout = $payoutsToRealise[] = $lastDeposit->getDefaultPayout();
            $amount = ($lastDeposit->getAmount() * $lastPayoutPercent) / 100;
            $payout->setExpectedAmount($amount);
            if ($amount > $balance) {
                $amount = $balance;
            }
            $payout->setAmount($amount);
            $payout->setFee(($payout->getAmount() * $this->round->getPayoutFeePercent()) / 100);
            $payout->setType(Payout::TYPE_LAST_PAYOUT);
            $payout->setReadyForPayout(true);
            $this->em->persist($payout);
            $this->em->flush();
            $balance -= $payout->getAmount() + $payout->getFee();
        }
        if ($balance > 0) {
            // admin last payout
            $adminLastPayoutPercent = $this->round->getAdminLastPayoutPercent();
            if ($adminLastPayoutPercent > 0) {
                $payout = new Payout();
                $amount = ($balance * $adminLastPayoutPercent) / 100;
                $payout->setExpectedAmount($amount);
                if ($amount > $balance) {
                    $amount = $balance;
                }
                $payout->setAmount($amount);
                $payout->setFee(0);
                $payout->setType(Payout::TYPE_LAST_ADMIN_PAYOUT);
                $payout->setReadyForPayout(true);
                $this->em->persist($payout);
                $this->em->flush();
                $payout->setPaid(true); // not sending it will simply stay on the wallet account for later manual withdrawal
                $this->em->flush();
                $balance -= $payout->getAmount() + $payout->getFee();
            }
        }

        // remaining payouts
        $payouts = $this->payoutRepo->findForPayout($this->round, 'desc');
        $payoutPercent = $this->round->getRoundEndRemainingReturnPercent();

        foreach ($payouts as $payout) {

            if ($payout->isLastPayout()) {
                // already in $payoutsToRealise array
                continue;
            }
            if ($balance > 0) {

                $amount = ($payout->getDeposit()->getAmount() * $payoutPercent) / 100;

                $payout->isReferrerPayout() && $amount = ($amount * $this->round->getReferrerPayoutPercent()) / 100;
                $payout->setExpectedAmount($amount);
                if ($amount > $balance) {
                    $amount = $balance;
                }
                $payout->setAmount($amount);
                $payout->setFee(($payout->getAmount() * $this->round->getPayoutFeePercent()) / 100);
                $payoutsToRealise[] = $payout;
                $balance -= $payout->getAmount() + $payout->getFee();
            }
        }
        $this->_realisePayouts($payoutsToRealise);

        $this->em->flush();
    }

    /**
     * @param array|Payout[] $payouts
     * @return array|Payout[]
     * @throws \Bml\CoinBundle\Exception\RequestException
     * @throws \Exception
     */
    private function _realisePayouts(array $payouts)
    {
        $sumPayout = 0;
        foreach ($payouts as $payout) {
            $sumPayout += $payout->getAmount();
        }
        do {
            if ($sumPayout < $this->round->getMinDeposit()) {
                // we will not payout less than min deposit
                // this is probably only paying out referrer
                // referrer payout amount is less than payout tx fee
                // it makes no sense to pay it out alone
                return [];
            }
            try {
                $tx = $this->walletManager->sendPayouts($payouts);
            } catch (RequestException $e) {
                if ($e->getCode() != -6) {
                    throw $e;
                }
                // this is probably insufficient founds error
                // that's because of fee added by client
                // we will try to pop last payout to see if then we will fit within fee+payout_amount<balance
                $last = array_pop($payouts);
                /* @var $payout Payout */
                $sumPayout -= $last->getAmount();
                continue;
            }
            foreach ($payouts as $payout) {
                $payout->setPaid(true);
                $payout->setTx($tx);
                $payout->setPaidOutTime(new \DateTime());
            }
            $this->em->flush();
            break;
        } while (!empty($payouts));
        return $payouts;
    }
}
