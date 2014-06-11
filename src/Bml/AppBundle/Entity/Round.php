<?php

namespace Bml\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Round
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Bml\AppBundle\Entity\RoundRepository")
 */
class Round
{

    /**
     * round finishes after $this::roundTime time passes after last payout
     */
    const ROUND_TIME_TYPE_LAST_PAYOUT = 0;

    /**
     * round finishes after $this::roundTime time passes after last deposit
     */
    const ROUND_TIME_TYPE_LAST_DEPOSIT = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Stats
     *
     * @ORM\OneToOne(targetEntity="Bml\AppBundle\Entity\Stats", cascade={"persist"}, inversedBy="round")
     * @ORM\JoinColumn(nullable=false)
     */
    private $stats;


    /**
     * @var boolean
     *
     * @ORM\Column(name="finished", type="boolean", nullable=false)
     *
     */
    private $finished = false;

    /**
     * @var boolean
     *
     * @ORM\Column(name="started", type="boolean", nullable=false)
     */
    private $started = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="minConfirmations", type="integer", nullable=false)
     *
     * @Assert\NotNull
     */
    private $minConfirmations = 2;

    /**
     * @var float
     *
     * @ORM\Column(name="payoutPercent", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $payoutPercent = 120;

    /**
     * @var float
     *
     * @ORM\Column(name="lastPayoutPercent", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $lastPayoutPercent = 200;

    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $adminLastPayoutPercent = 10;

    /**
     * @var float
     *
     * @ORM\Column(name="referrerPayoutPercent", type="float", nullable=false)
     */
    private $referrerPayoutPercent = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="minDeposit", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $minDeposit = 0.001;

    /**
     * @var float
     *
     * @ORM\Column(name="maxDeposit", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $maxDeposit = 0.1;

    /**
     * @var float
     *
     * @ORM\Column(name="payoutFeePercent", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $payoutFeePercent = 1;

    /**
     * @var float
     *
     * @ORM\Column(name="roundEndRemainingReturnPercent", type="float", nullable=false)
     *
     * @Assert\NotNull
     */
    private $roundEndRemainingReturnPercent = 100;

    /**
     * @var integer in hours
     *
     * @ORM\Column(name="roundTime", type="integer", nullable=false)
     *
     * @Assert\NotNull
     */
    private $roundTime = 48;

    /**
     * @var integer
     * @ORM\Column(name="roundTimeType", type="integer", nullable=false)
     *
     * @Assert\NotNull
     */
    private $roundTimeType = self::ROUND_TIME_TYPE_LAST_PAYOUT;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $walletAccount;

    public function __construct()
    {
        $this->stats = new Stats();
        $this->stats->setRound($this);
    }


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
     * @param int $value
     * @return $this
     */
    private function setId($value)
    {
        $this->id = $value;
        return $this;
    }

    /**
     * Get finished
     *
     * @return boolean
     */
    public function getFinished()
    {
        return $this->finished;
    }

    /**
     * Set finished
     *
     * @param boolean $finished
     * @return Round
     */
    public function setFinished($finished)
    {
        $this->finished = $finished;

        return $this;
    }

    /**
     * Get stats
     *
     * @return Stats
     */
    public function getStats()
    {
        return $this->stats;
    }

    /**
     * Set stats
     *
     * @param Stats $stats
     * @return Round
     */
    public function setStats(Stats $stats)
    {
        $this->stats = $stats;

        return $this;
    }

    /**
     * Get minConfirmations
     *
     * @return integer
     */
    public function getMinConfirmations()
    {
        return $this->minConfirmations;
    }

    /**
     * Set minConfirmations
     *
     * @param integer $minConfirmations
     * @return Round
     */
    public function setMinConfirmations($minConfirmations)
    {
        $this->minConfirmations = $minConfirmations;

        return $this;
    }

    /**
     * Get payoutPercent
     *
     * @return float
     */
    public function getPayoutPercent()
    {
        return $this->payoutPercent;
    }

    /**
     * Set payoutPercent
     *
     * @param float $payoutPercent
     * @return Round
     */
    public function setPayoutPercent($payoutPercent)
    {
        $this->payoutPercent = $payoutPercent;

        return $this;
    }

    /**
     * Get lastPayoutPercent
     *
     * @return float
     */
    public function getLastPayoutPercent()
    {
        return $this->lastPayoutPercent;
    }

    /**
     * Set lastPayoutPercent
     *
     * @param float $lastPayoutPercent
     * @return Round
     */
    public function setLastPayoutPercent($lastPayoutPercent)
    {
        $this->lastPayoutPercent = $lastPayoutPercent;

        return $this;
    }

    /**
     * Get referrerPayoutPercent
     *
     * @return float
     */
    public function getReferrerPayoutPercent()
    {
        return $this->referrerPayoutPercent;
    }

    /**
     * Set referrerPayoutPercent
     *
     * @param float $referrerPayoutPercent
     * @return Round
     */
    public function setReferrerPayoutPercent($referrerPayoutPercent)
    {
        $this->referrerPayoutPercent = $referrerPayoutPercent;

        return $this;
    }

    /**
     * Get minDeposit
     *
     * @return float
     */
    public function getMinDeposit()
    {
        return $this->minDeposit;
    }

    /**
     * Set minDeposit
     *
     * @param float $minDeposit
     * @return Round
     */
    public function setMinDeposit($minDeposit)
    {
        $this->minDeposit = $minDeposit;

        return $this;
    }

    /**
     * Get maxDeposit
     *
     * @return float
     */
    public function getMaxDeposit()
    {
        return $this->maxDeposit;
    }

    /**
     * Set maxDeposit
     *
     * @param float $maxDeposit
     * @return Round
     */
    public function setMaxDeposit($maxDeposit)
    {
        $this->maxDeposit = $maxDeposit;

        return $this;
    }

    /**
     * Get payoutFeePercent
     *
     * @return float
     */
    public function getPayoutFeePercent()
    {
        return $this->payoutFeePercent;
    }

    /**
     * Set payoutFeePercent
     *
     * @param float $payoutFeePercent
     * @return Round
     */
    public function setPayoutFeePercent($payoutFeePercent)
    {
        $this->payoutFeePercent = $payoutFeePercent;

        return $this;
    }

    /**
     * Get roundEndRemainingReturnPercent
     *
     * @return float
     */
    public function getRoundEndRemainingReturnPercent()
    {
        return $this->roundEndRemainingReturnPercent;
    }

    /**
     * Set roundEndRemainingReturnPercent
     *
     * @param float $roundEndRemainingReturnPercent
     * @return Round
     */
    public function setRoundEndRemainingReturnPercent($roundEndRemainingReturnPercent)
    {
        $this->roundEndRemainingReturnPercent = $roundEndRemainingReturnPercent;

        return $this;
    }

    /**
     * @return boolean
     */
    public function getStarted()
    {
        return $this->started;
    }

    /**
     * @param boolean $started
     */
    public function setStarted($started)
    {
        $this->started = $started;
    }

    /**
     * @return int
     */
    public function getRoundTime()
    {
        return $this->roundTime;
    }

    /**
     * @param int $roundTime
     */
    public function setRoundTime($roundTime)
    {
        $this->roundTime = $roundTime;
    }

    /**
     * @return int
     */
    public function getRoundTimeType()
    {
        return $this->roundTimeType;
    }

    /**
     * @param int $roundTimeType
     */
    public function setRoundTimeType($roundTimeType)
    {
        $this->roundTimeType = $roundTimeType;
    }

    /**
     * @param float $adminPayoutPercent
     */
    public function setAdminLastPayoutPercent($adminPayoutPercent)
    {
        $this->adminLastPayoutPercent = $adminPayoutPercent;
    }

    /**
     * @return float
     */
    public function getAdminLastPayoutPercent()
    {
        return $this->adminLastPayoutPercent;
    }

    /**
     * @param string $walletAccount
     */
    public function setWalletAccount($walletAccount)
    {
        $this->walletAccount = $walletAccount;
    }

    /**
     * @return string
     */
    public function getWalletAccount()
    {
        return $this->walletAccount;
    }



    public function __clone()
    {
        if ($this->getId()) {
            $this->setId(null);
            $this->setStats(new Stats());
        }
    }



}
