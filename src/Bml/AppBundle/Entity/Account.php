<?php

namespace Bml\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 *
 * @ORM\Table(
 *   uniqueConstraints={@ORM\UniqueConstraint(name="unique_address",columns={"depositAddress"})},
 *    indexes={@ORM\Index(name="idx_status", columns={"depositAddress"})}
 * )
 * @ORM\Entity(repositoryClass="Bml\AppBundle\Entity\AccountRepository")
 */
class Account implements RoundedEntityInterface
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
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Account")
     */
    private $referrer;

    /**
     * @var Account
     *
     * @ORM\OneToMany(targetEntity="Bml\AppBundle\Entity\Deposit", mappedBy="account")
     */
    private $deposits;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=44)
     */
    private $depositAddress;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=44)
     */
    private $withdrawAddress;

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
     * @ORM\Column(type="integer")
     */
    private $totalReferralPayoutCount = 0;

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
    private $totalUnpaidReferralEarnings = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $totalUnpaidReferralEarningsCount = 0;


    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     */
    private $referralRegistersCount = 0;


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
     * @param string $depositAddress
     * @return $this
     */
    public function setDepositAddress($depositAddress)
    {
        $this->depositAddress = $depositAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getDepositAddress()
    {
        return $this->depositAddress;
    }


    /**
     * @return \Bml\AppBundle\Entity\Account
     */
    public function getDeposits()
    {
        return $this->deposits;
    }

    /**
     * @return $this
     */
    public function addReferralRegistersCount()
    {
        $this->referralRegistersCount++;
        return $this;
    }

    /**
     * @return int
     */
    public function getReferralRegistersCount()
    {
        return $this->referralRegistersCount;
    }

    /**
     * @param \Bml\AppBundle\Entity\Account $referrer
     * @return $this
     */
    public function setReferrer($referrer)
    {
        $this->referrer = $referrer;
        return $this;
    }

    /**
     * @return \Bml\AppBundle\Entity\Account
     */
    public function getReferrer()
    {
        return $this->referrer;
    }

    /**
     * @param float $value
     * @return $this
     */
    public function addTotalDeposit($value)
    {
        $this->totalDeposit += $value;
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
    public function addTotalPayout($value)
    {
        $this->totalPayout += $value;
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
     * @return float
     */
    public function getTotalPendingDeposit()
    {
        return $this->totalPendingDeposit;
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
    public function addTotalReferralPayout($value)
    {
        $this->totalReferralPayout += $value;
        $this->totalReferralPayoutCount++;
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
    public function addTotalUnpaidReferralEarnings($value)
    {
        $this->totalUnpaidReferralEarnings += $value;
        $this->totalUnpaidReferralEarningsCount++;
        return $this;
    }

    /**
     * @return float
     */
    public function getTotalUnpaidReferralEarnings()
    {
        return $this->totalUnpaidReferralEarnings;
    }


    /**
     * @return int
     */
    public function getTotalUnpaidReferralEarningsCount()
    {
        return $this->totalUnpaidReferralEarningsCount;
    }

    /**
     * @param string $withdrawAddress
     * @return $this
     */
    public function setWithdrawAddress($withdrawAddress)
    {
        $this->withdrawAddress = $withdrawAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getWithdrawAddress()
    {
        return $this->withdrawAddress;
    }

    /**
     * @return $this
     */
    public function setReferralEarningsPaid()
    {
        $this->totalReferralPayout += $this->totalUnpaidReferralEarnings;
        $this->totalReferralPayoutCount += $this->totalUnpaidReferralEarningsCount;
        $this->totalUnpaidReferralEarningsCount = $this->totalUnpaidReferralEarnings = 0;
        return $this;
    }

    /**
     * @param \Bml\AppBundle\Entity\Round $round
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
