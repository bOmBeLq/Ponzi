<?php


namespace Bml\AppBundle\ORM\DataFixtures;

use Bml\AppBundle\Entity\Account;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\DataFixtures
 */
class AccountFixtures extends AbstractFixture implements OrderedFixtureInterface
{


    public function load(ObjectManager $manager)
    {
        $acc1 = new Account();
        $acc1->setWithdrawAddress('ms75uVn7bkC2aDoMGWtoKnCiFLvvCWkhPA');
        $acc1->setDepositAddress('mwD4ax4J25ZGwvhht3PDhemBKRyj4qsCZ7');
        $manager->persist($acc1);

        $acc2 = new Account();
        $acc2->setWithdrawAddress('mnm4M2DFaDAWmLNfQ2dJXxXpofbzhqXvUM');
        $acc2->setDepositAddress('mqUy9FUwaPjXZjYyW3UjsTpYMHdckq6kin');
        $acc2->setReferrer($acc1);
        $manager->persist($acc2);

        $acc3 = new Account();
        $acc3->setWithdrawAddress('mxMEGiP93LEAXwgx548HizWTxc5jr42bgq');
        $acc3->setDepositAddress('n4avMwZWyqXbNgxxtfmdMSu1nQfhLcWPR9');
        $acc3->setReferrer($acc1);
        $manager->persist($acc3);

        $acc4 = new Account();
        $acc4->setWithdrawAddress('n13WFTuHsaeC4kFotfu5v9E6GcUbnkT5Po');
        $acc4->setDepositAddress('mqqcH6zWoKs8UcuY3ZfwANYxFqBiRJWL9F');
        $acc4->setReferrer($acc2);
        $manager->persist($acc4);

        $manager->flush();
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        return 2;
    }
}
