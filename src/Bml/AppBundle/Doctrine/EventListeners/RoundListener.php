<?php


namespace Bml\AppBundle\Doctrine\EventListeners;

use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundedEntityInterface;use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Doctrine\ORM\UnitOfWork;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Doctrine\EventListeners
 */
class RoundListener
{

    /**
     * @var Round
     */
    private $round;

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
        foreach ($this->uow->getScheduledEntityInsertions() as $entity) {
            if ($entity instanceof RoundedEntityInterface) {
                $entity->setRound($this->round);
                $this->uow->recomputeSingleEntityChangeSet($this->em->getClassMetadata(get_class($entity)), $entity);
            }
        }
        //$this->uow->computeChangeSets();
    }

    /**
     * @param OnFlushEventArgs $e
     */
    private function init(OnFlushEventArgs $e)
    {
        $this->em = $e->getEntityManager();
        $this->uow = $this->em->getUnitOfWork();
        $roundRepo = $this->em->getRepository('AppBundle:Round');
        /* @var $roundRepo \Bml\AppBundle\Entity\RoundRepository */
        $this->round = $roundRepo->findCurrent();

    }
}
