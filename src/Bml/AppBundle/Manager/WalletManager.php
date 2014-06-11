<?php
/**
 * Created by PhpStorm.
 * User: bml
 * Date: 02.05.14
 * Time: 14:46
 */

namespace Bml\AppBundle\Manager;


use Bml\AppBundle\Entity\Deposit;
use Bml\AppBundle\Entity\DepositRepository;
use Bml\AppBundle\Entity\Payout;
use Bml\AppBundle\Entity\PayoutTx;
use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\CoinBundle\Entity\TransactionListResult;
use Bml\CoinBundle\Exception\RequestException;
use Bml\CoinBundle\Manager\CoinManager;
use Bml\CoinBundle\Manager\CoinManagerContainer;
use Doctrine\ORM\EntityManager;

class WalletManager
{

    /**
     * @var CoinManager
     */
    private $manager;

    /**
     * @var DepositRepository
     */
    private $depositRepo;

    /**
     * @var Round
     */
    private $round;


    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    public function __construct(CoinManagerContainer $coinManagerContainer, EntityManager $em,
                         DepositRepository $depositRepo, RoundRepository $roundRepo)
    {
        $this->depositRepo = $depositRepo;
        $this->manager = $coinManagerContainer->get('main');
        $this->round = $roundRepo->findCurrent();
        $this->em = $em;
    }


    /**
     * @return array|\Bml\CoinBundle\Entity\TransactionListResult[]
     */
    public function getNewIncomingTransactions()
    {
        $round = $this->round;
        $newTransactions = [];
        /* @var $newTransactions \Bml\CoinBundle\Entity\TransactionListResult[] */

        $from = 0;
        $step = 10;
        do {
            $transactions = $this->manager->listTransactions($round->getWalletAccount(), $step, $from);

            $transactions = array_reverse($transactions);
            /* @var $transactions TransactionListResult[] */
            foreach ($transactions as $transaction) {
                if ($transaction->getCategory() != TransactionListResult::CATEGORY_RECEIVE) {
                    continue;
                }
                if ($transaction->getTxId() == $round->getStats()->getLastCheckedTx() || !$transactions) {
                    break 2;
                }
                if (!$this->depositRepo->findOneBy(['txIn' => $transaction->getTxId(), 'round' => $this->round])) {
                    $newTransactions[] = $transaction;
                }
            }
            $from += $step;
        } while (count($transactions));

        $newTransactions = array_reverse($newTransactions);
        return !empty($newTransactions) ? $newTransactions : null;
    }

    /**
     * @param Deposit $deposit
     * @return int
     */
    public function getConfirmations(Deposit $deposit)
    {
        $info = $this->manager->getTransaction($deposit->getTxIn());
        return $info->getConfirmations();
    }

    /**
     * @param Payout[] $payouts
     * @return PayoutTx the tx out
     */
    public function sendPayouts(array $payouts)
    {
        $amounts = [];
        foreach ($payouts as $payout) {
            if (!isset($amounts[$payout->getAccount()->getWithdrawAddress()])) {
                $amounts[$payout->getAccount()->getWithdrawAddress()] = 0;
            }
            $amounts[$payout->getAccount()->getWithdrawAddress()] += $payout->getAmount();
        }

        $tx = $this->manager->sendMany($this->round->getWalletAccount(), $amounts);
        $walletTx = $this->manager->getTransaction($tx);
        $tx = new PayoutTx();
        $tx->setTxFee($walletTx->getFee());
        $tx->setTxOut($walletTx->getTxId());
        $this->em->persist($tx);
        $this->em->flush();
        return $tx;
    }
}
