<?php

namespace Bml\AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit
 *
 * @ORM\Table(
 *      uniqueConstraints={@ORM\UniqueConstraint(name="unique_tx",columns={"txIn"})})}
 * )
 * @ORM\Entity(repositoryClass="Bml\AppBundle\Entity\DepositRepository")
 */
class Deposit implements RoundedEntityInterface
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
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Account", inversedBy="deposits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $account;

    /**
     * @var Payout[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Bml\AppBundle\Entity\Payout", mappedBy="deposit")
     */
    private $payouts;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime")
     */
    private $receivedTime;


    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $amount;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $txIn;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $confirmations;

    /**
     * @var integer
     *
     * @ORM\Column(type="boolean")
     */
    private $confirmed = false;

    public function __construct()
    {
        $this->payouts = new ArrayCollection();
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
     * @param \DateTime $receivedTime
     * @return $this
     */
    public function setReceivedTime($receivedTime)
    {
        $this->receivedTime = $receivedTime;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getReceivedTime()
    {
        return $this->receivedTime;
    }


    /**
     * Set amount
     *
     * @param float $amount
     * @return Deposit
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set txIn
     *
     * @param string $txIn
     * @return Deposit
     */
    public function setTxIn($txIn)
    {
        $this->txIn = $txIn;

        return $this;
    }

    /**
     * Get txIn
     *
     * @return string
     */
    public function getTxIn()
    {
        return $this->txIn;
    }

    /**
     * Set confirmations
     *
     * @param integer $confirmations
     * @return Deposit
     */
    public function setConfirmations($confirmations)
    {
        $this->confirmations = $confirmations;

        return $this;
    }

    /**
     * Get confirmations
     *
     * @return integer
     */
    public function getConfirmations()
    {
        return $this->confirmations;
    }

    /**
     * @param int $isConfirmed
     */
    public function setConfirmed($isConfirmed)
    {
        $this->confirmed = $isConfirmed;
    }

    /**
     * @return int
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }



    /**
     * @param \Bml\AppBundle\Entity\Account $account
     * @return $this
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }

    /**
     * @return \Bml\AppBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
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


    /**
     * @return \Bml\AppBundle\Entity\Payout[]|ArrayCollection
     */
    public function getPayouts()
    {
        return $this->payouts;
    }

    /**
     * @return Payout|null
     */
    public function getDefaultPayout()
    {
        foreach ($this->payouts as $payout) {
            if ($payout->isDefaultPayout()) {
                return $payout;
            }
        }
        return null;
    }

    /**
     * @return Payout|null
     */
    public function getReferrerPayout()
    {
        foreach ($this->payouts as $payout) {
            if ($payout->isReferrerPayout()) {
                return $payout;
            }
        }
        return null;
    }
}
