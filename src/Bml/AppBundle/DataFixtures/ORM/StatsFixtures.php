<?php


namespace Bml\AppBundle\ORM\DataFixtures;

use Bml\AppBundle\Entity\Account;
use Bml\AppBundle\Entity\Stats;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\DataFixtures
 */
class StatsFixtures extends AbstractFixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        //$stats = new Stats();
        //$manager->persist($stats);

        //$manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 1;
    }
}
