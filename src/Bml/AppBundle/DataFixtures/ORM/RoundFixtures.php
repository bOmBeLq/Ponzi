<?php


namespace Bml\AppBundle\ORM\DataFixtures;

use Bml\AppBundle\Entity\Account;
use Bml\AppBundle\Entity\Round;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\DataFixtures
 */
class RoundFixtures extends AbstractFixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $round = new Round();
        $round->setStarted(true);
        $round->setWalletAccount('ponzi');
        $manager->persist($round);
        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 0;
    }
}
