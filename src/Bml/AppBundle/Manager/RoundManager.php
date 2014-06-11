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

class RoundManager
{

    /**
     * @var string
     */
    private $walletAccountPrefix;

    /**
     * @var EntityManager
     */
    private $em;


    /**
     * @var RoundRepository
     */
    private $roundRepo;

    public function __construct(EntityManager $em, RoundRepository $roundRepo, $walletAccountPrefix)
    {
        $this->em = $em;
        $this->walletAccountPrefix = $walletAccountPrefix;
        $this->roundRepo = $roundRepo;
    }


    public function defineWalletAccount(Round $round)
    {
        $round->setWalletAccount($this->walletAccountPrefix . $round->getId());
        $this->em->flush($round);
    }

    public function startNextRound()
    {
        $currentRound = $this->roundRepo->findCurrent();
        // check if settings for next round exist
        if (!$nextRound = $this->roundRepo->find($currentRound->getId() + 1)) {
            $nextRound = clone $currentRound; // __clone removes id
            $this->em->persist($nextRound);
        }
        $currentRound->setFinished(true);
        $nextRound->setStarted(true);
        $this->em->flush();
    }
}
