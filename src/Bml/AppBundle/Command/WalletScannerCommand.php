<?php


namespace Bml\AppBundle\Command;

use Bml\AppBundle\Entity\Round;
use Bml\AppBundle\Entity\RoundRepository;
use Bml\AppBundle\Lock\Lock;
use Bml\AppBundle\Lock\UnableToLockException;
use Bml\AppBundle\Manager\DepositManager;
use Bml\AppBundle\Manager\PayoutManager;
use Bml\AppBundle\Manager\RoundManager;
use Bml\AppBundle\Manager\WalletManager;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package AppBundle\Command
 */
class WalletScannerCommand extends ContainerAwareCommand
{
    /**
     * @var EntityManager
     */
    private $em;


    /**
     * @var Round
     */
    private $round;

    /**
     * @var RoundRepository
     */
    private $roundRepo;

    /**
     * @var OutputInterface
     */
    private $output;

    /**
     * @var InputInterface
     */
    private $input;

    /**
     * @var string
     */
    private $nameShort;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var WalletManager
     */
    private $walletManager;

    /**
     * @var DepositManager
     */
    private $depositManager;

    /**
     * @var PayoutManager
     */
    private $payoutManager;

    /**
     * @var RoundManager
     */
    private $roundManager;

    private function init()
    {
        $this->depositManager = $this->getContainer()->get('bml.deposit.manager');
        $this->payoutManager = $this->getContainer()->get('bml.payout.manager');
        $this->walletManager = $this->getContainer()->get('bml.wallet.manager');
        $this->roundManager = $this->getContainer()->get('bml.round.manager');;
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->logger = $this->getContainer()->get('logger');
        $this->roundRepo = $this->getContainer()->get('bml.round.repo');;
        $this->nameShort = $this->getContainer()->getParameter('currency_short');


    }


    protected function configure()
    {
        $this
            ->setName('bml:wallet:scan');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init();
        $this->output = $output;
        $this->input = $input;

        try {
            $lock = Lock::lock('scanner_' . $this->nameShort);
        } catch (UnableToLockException $e) {
            $this->writeln('Already running');
            return;
        };
        $this->writeln('Started wallet scanner ' . date('Y-m-d H:i:s'));


        if (!$this->round = $this->roundRepo->findOneBy(['started' => true, 'finished' => false])) {
            $this->writeln('No running round');
            return;
        }

        $this->writeln('Creating new incoming transactions');
        if ($newTransactions = $this->walletManager->getNewIncomingTransactions()) {

            $this->writeln('Found ' . count($newTransactions) . ' new transactions');
            $this->depositManager->saveNewDeposits($newTransactions);
        } else {
            $this->writeln('No new transactions found');
        }
        $this->writeln('Updating deposits');
        $updated = $this->depositManager->updatePendingDeposits();
        $this->writeln('Updated/checked ' . $updated . ' deposits');
        $this->writeln('Realising payouts');
        $payouts = $this->payoutManager->realisePayouts();
        $this->writeln('Realised ' . count($payouts) . ' payouts');
        $this->writeln('Checking round end');
        $this->checkRoundEnd();
    }

    private function checkRoundEnd()
    {
        $time1 = new \DateTime();
        $time1->modify('-' . $this->round->getRoundTime() . ' hours');
        if ($this->round->getRoundTimeType() == Round::ROUND_TIME_TYPE_LAST_DEPOSIT) {
            $time2 = $this->round->getStats()->getLastDeposit();
        } else {
            $time2 = $this->round->getStats()->getLastPayout();
        }
        if ($time2 && $time1 > $time2) {
            $this->writeln('Round finished');
            $this->payoutManager->realiseRoundEndPayouts();
            $this->roundManager->startNextRound();
        } else {
            $this->writeln('Round not finished');
        }
    }

    /**
     * @param string $text
     */
    private function writeLn($text)
    {
        $this->output->writeln($text);
        $this->logger->addInfo($text);
    }
}
