<?php


namespace Bml\AppBundle\Entity;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Entity
 * defines entity which should be categorized by round
 */
interface RoundedEntityInterface
{
    /**
     *
     * @return int
     */
    public function getRound();

    /**
     * @param \Bml\AppBundle\Entity\Round $round
     * @return $this
     */
    public function setRound(Round $round);
}
