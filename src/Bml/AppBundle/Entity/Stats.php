<?php

namespace Bml\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Stats
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bml\AppBundle\Entity\StatsRepository")
 */
class Stats
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\OneToOne(targetEntity="Bml\AppBundle\Entity\Round", mappedBy="stats")
     */
    private $round;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalDeposit = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $totalDepositCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalPendingDeposit = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $totalPendingDepositCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalPayout = 0;


    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $totalPayoutCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalReferralPayout = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", precision=60, scale=8, nullable=false)
     */
    private $totalReferralPayoutCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalTxFees = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalFees = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalDonation = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $totalDonationCount = 0;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $balance = 0;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastDeposit;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastPayout;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $lastCheckedTx;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $roundFinished = false;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return float
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * @param string $lastCheckedTx
     * @return $this
     */
    public function setLastCheckedTx($lastCheckedTx)
    {
        $this->lastCheckedTx = $lastCheckedTx;
        return $this;
    }

    /**
     * @return string
     */
    public function getLastCheckedTx()
    {
        return $this->lastCheckedTx;
    }

    /**
     * @param \DateTime $lastDeposit
     * @return $this
     */
    public function setLastDeposit(\DateTime $lastDeposit)
    {
        $this->lastDeposit = $lastDeposit;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastDeposit()
    {
        return $this->lastDeposit;
    }

    /**
     * @param \DateTime $lastPayout
     * @return $this
     */
    public function setLastPayout(\DateTime $lastPayout)
    {
        $this->lastPayout = $lastPayout;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getLastPayout()
    {
        return $this->lastPayout;
    }

    /**
     * @param float $amount
     * @return $this
     */
    public function addTotalDeposit($amount)
    {
        $this->balance += $amount;
        $this->totalDeposit += $amount;
        $this->totalDepositCount++;

        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDeposit()
    {
        return $this->totalDeposit;
    }

    /**
     * @return int
     */
    public function getTotalDepositCount()
    {
        return $this->totalDepositCount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalDonation($value)
    {
        $this->totalDonation += $value;
        $this->totalDonationCount++;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalDonation()
    {
        return $this->totalDonation;
    }

    /**
     * @return float
     */
    public function getTotalDonationCount()
    {
        return $this->totalDonationCount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalFees($value)
    {
        $this->totalFees += $value;
        $this->balance -= $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalFees()
    {
        return $this->totalFees;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalPayout($value)
    {
        $this->totalPayout += $value;
        $this->balance -= $value;
        $this->totalPayoutCount++;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPayout()
    {
        return $this->totalPayout;
    }


    /**
     * @return int
     */
    public function getTotalPayoutCount()
    {
        return $this->totalPayoutCount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalPendingDeposit($value)
    {
        $this->totalPendingDeposit += $value;
        $this->totalPendingDepositCount++;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalPendingDeposit()
    {
        return $this->totalPendingDeposit;
    }


    /**
     * @param float $value
     * @return $this
     */
    public function subtractTotalPendingDeposit($value)
    {
        $this->totalPendingDeposit -= $value;
        $this->totalPendingDepositCount--;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalPendingDepositCount()
    {
        return $this->totalPendingDepositCount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalTxFees($value)
    {
        $this->totalTxFees += $value;
        $this->balance -= $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalTxFees()
    {
        return $this->totalTxFees;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalReferralPayout($value)
    {
        $this->totalReferralPayout += $value;
        $this->balance -= $value;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalReferralPayout()
    {
        return $this->totalReferralPayout;
    }


    /**
     * @return int
     */
    public function getTotalReferralPayoutCount()
    {
        return $this->totalReferralPayoutCount;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalReferralPayoutCount($value)
    {
        $this->totalReferralPayoutCount += $value;
        return $this;
    }

    /**
     * @param boolean $roundFinished
     * @return $this
     */
    public function setRoundFinished($roundFinished)
    {
        $this->roundFinished = $roundFinished;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getRoundFinished()
    {
        return $this->roundFinished;
    }

    /**
     * @param Round $round
     * @return $this
     */
    public function setRound(Round $round)
    {
        $this->round = $round;
        return $this;
    }

    /**
     * @return int
     */
    public function getRound()
    {
        return $this->round;
    }


}
