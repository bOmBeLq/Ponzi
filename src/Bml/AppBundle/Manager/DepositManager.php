<?php
/**
 * Created by PhpStorm.
 * User: bml
 * Date: 02.05.14
 * Time: 14:50
 */

namespace Bml\AppBundle\Manager;


use Bml\AppBundle\Entity\Account;
use Bml\AppBundle\Entity\AccountRepository;
use Bml\AppBundle\Entity\Deposit;
use Bml\AppBundle\Entity\DepositRepository;
use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\CoinBundle\Entity\TransactionListResult;
use Doctrine\ORM\EntityManager;

class DepositManager
{

    /**
     * @var WalletManager
     */
    private $walletManager;


    /**
     * @var Round
     */
    private $round;

    /**
     * @var AccountRepository
     */
    private $accountRepo;

    /**
     * @var DepositRepository
     */
    private $depositRepo;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var PayoutManager
     */
    private $payoutManager;

    public function __construct(WalletManager $walletManager, AccountRepository $accountRepo, RoundRepository $roundRepo,
                         DepositRepository $depositRepo, PayoutManager $payoutManager, EntityManager $em)
    {
        $this->accountRepo = $accountRepo;
        $this->round = $roundRepo->findCurrent();
        $this->em = $em;
        $this->walletManager = $walletManager;
        $this->depositRepo = $depositRepo;
        $this->payoutManager = $payoutManager;
    }

    /**
     * @return int
     */
    public function updatePendingDeposits()
    {
        $deposits = $this->depositRepo->findBy(['confirmed' => false, 'round' => $this->round], ['id' => 'asc']);
        foreach ($deposits as $deposit) {
            $confirmations = $this->walletManager->getConfirmations($deposit);

            $deposit->setConfirmations($confirmations);
            if ($deposit->getConfirmations() >= $this->round->getMinConfirmations()) {
                $deposit->setConfirmed(true);
                // updating payout status
                // we could put this in listener together with persist
                // but it does not matter cause this status is updated only in 3 places
                foreach ($deposit->getPayouts() as $payout) {
                    $payout->setReadyForPayout(true);
                }
            }
        }
        $this->em->flush();
        return count($deposits);
    }

    /**
     * @param array|TransactionListResult[] $transactions
     * @return Deposit[]
     */
    public function saveNewDeposits(array $transactions)
    {
        $deposits = [];
        foreach ($transactions as $transaction) {
            $this->round->getStats()->setLastCheckedTx($transaction->getTxId());
            if ($account = $this->accountRepo->findOneBy(['depositAddress' => $transaction->getAddress(), 'round' => $this->round])) {
                /* @var $account Account */
                if ($transaction->getAmount() < $this->round->getMinDeposit()) {
                    $this->round->getStats()->addTotalDonation($transaction->getAmount());
                    continue;
                }
                $amount = min($this->round->getMaxDeposit(), $transaction->getAmount());
                $donation = ($transaction->getAmount() - $amount);
                if ($donation) {
                    $this->round->getStats()->addTotalDonation($donation);
                }
                $deposit = $deposits[] = new Deposit();
                $deposit->setAmount($amount);
                $deposit->setConfirmations($transaction->getConfirmations());
                $deposit->setReceivedTime($transaction->getTime());
                if ($deposit->getConfirmations() >= $this->round->getMinConfirmations()) {
                    $deposit->setConfirmed(true);
                }
                $deposit->setTxIn($transaction->getTxId());
                $deposit->setAccount($account);
                $this->payoutManager->createPayout($deposit);
                $this->em->persist($deposit);
                $this->em->flush();
            }
        }
        $this->em->flush();
        return $deposits;
    }
}
