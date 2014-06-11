<?php


namespace Bml\AppBundle\Doctrine\EventListeners;

use Bml\AppBundle\Entity\Deposit;
use Bml\AppBundle\Entity\Payout;
use Bml\AppBundle\Entity\PayoutTx;
use Bml\AppBundle\Entity\ReferrerPayout;
use Bml\AppBundle\Entity\Stats;
use Doctrine\Common\EventArgs;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\UnitOfWork;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Doctrine\EventListeners
 */
class StatsListener
{

    /**
     * @var Stats
     */
    private $stats;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var UnitOfWork
     */
    private $uow;

    public function onFlush(OnFlushEventArgs $e)
    {
        $this->init($e);
        if ($this->stats) {
            foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
                if ($entity instanceof Deposit) {
                    // deposit arrived
                    if ($entity->isConfirmed()) {
                        // it is confirmed
                        $this->addConfirmedDeposit($entity);

                    } else {
                        // it is pending
                        $entity->getAccount()->addTotalPendingDeposit($entity->getAmount());
                        $this->stats->addTotalPendingDeposit($entity->getAmount());
                    }
                    $this->stats->setLastDeposit($entity->getReceivedTime());

                    $this->recoumputeChanges($entity->getAccount());
                }
                if ($entity instanceof PayoutTx) {
                    // transaction has just been sent
                    $this->stats->addTotalTxFees($entity->getTxFee() * -1);
                }
            }
            foreach ($this->uow->getScheduledEntityUpdates() as $entity) {
                if ($entity instanceof Deposit) {
                    $changes = $this->uow->getEntityChangeSet($entity);
                    if (isset($changes['confirmed'])) {
                        $oldConfirmed = $changes['confirmed'][0];
                        $newConfirmed = $changes['confirmed'][1];
                        if (!$oldConfirmed && $newConfirmed) {
                            // subtract pending if not referrer
                            $this->stats->subtractTotalPendingDeposit($entity->getAmount());
                            $entity->getAccount()->subtractTotalPendingDeposit($entity->getAmount());

                            // add confirmed
                            $this->addConfirmedDeposit($entity);

                            $this->recoumputeChanges($entity->getAccount());
                        }
                    }
                }
                if ($entity instanceof Payout) {
                    $changes = $this->uow->getEntityChangeSet($entity);
                    if (isset($changes['paid'])) {
                        $oldPaid = $changes['paid'][0];
                        $newPaid = $changes['paid'][1];

                        if (!$oldPaid && $newPaid) {
                            if ($entity->isDefaultPayout() || $entity->isRemainingFundsReturn() || $entity->isLastPayout()) {
                                $this->stats->addTotalPayout($entity->getAmount());
                                $entity->getAccount()->addTotalPayout($entity->getAmount());
                                if ($entity->isDefaultPayout()) {
                                    $this->stats->setLastPayout($entity->getPaidOutTime());
                                }
                            } elseif ($entity->isReferrerPayout()) {
                                $this->stats->addTotalReferralPayout($entity->getAmount());
                                $entity->getAccount()->addTotalReferralPayout($entity->getAmount());
                            }
                            if (!$entity->isLastAdminPayout()) {
                                $this->recoumputeChanges($entity->getAccount());
                            }
                            $this->stats->addTotalFees($entity->getFee());
                        }
                    }
                }
            }
            $this->recoumputeChanges($this->stats);
            $this->uow->computeChangeSets();
        }
    }

    /**
     * @param OnFlushEventArgs $e
     */
    private function init(OnFlushEventArgs $e)
    {
        $this->em = $e->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
        $this->stats = $this->em->getRepository('AppBundle:Stats')->find(1);
    }

    private function addConfirmedDeposit(Deposit $entity)
    {
        $entity->getAccount()->addTotalDeposit($entity->getAmount());
        $this->stats->addTotalDeposit($entity->getAmount());

        if ($entity->getReferrerPayout()) {
            $entity->getAccount()->addTotalUnpaidReferralEarnings($entity->getReferrerPayout()->getAmount());
        }
    }

    /**
     * @param $entity
     */
    private function recoumputeChanges($entity)
    {
        if ($this->uow->getEntityChangeSet($entity)) {
            $this->uow->recomputeSingleEntityChangeSet($this->em->getClassMetadata(get_class($entity)), $entity);
        }
    }
}
