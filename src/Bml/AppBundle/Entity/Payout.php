<?php


namespace Bml\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Deposit
 *
 * @ORM\Table(
 *      )
 * @ORM\Entity(repositoryClass="Bml\AppBundle\Entity\PayoutRepository")
 */
class Payout implements RoundedEntityInterface
{
    const TYPE_DEFAULT_PAYOUT = 'default';
    const TYPE_REFERRER_PAYOUT = 'referrer';
    const TYPE_LAST_PAYOUT = 'last payout';
    const TYPE_LAST_ADMIN_PAYOUT = 'last admin payout';
    const TYPE_REMAINING_FUNDS_RETURN = 'remaining funds';

    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Account
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Account", inversedBy="payouts")
     * @ORM\JoinColumn(nullable=true)
     */
    private $account;

    /**
     * @var Deposit
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Deposit", inversedBy="payouts")
     */
    private $deposit;

    /**
     * @var Round
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    /**
     * @var PayoutTx
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\PayoutTx")
     * @ORM\JoinColumn(nullable=true)
     */
    private $tx;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $paid = false;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $readyForPayout = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $paidOutTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * amount that was/will be paid
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $amount;

    /**
     * amount that should be paid if enough founds
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=false)
     */
    private $expectedAmount;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=true)
     */
    private $fee;


    /**
     * @var integer
     * @ORM\Column(type="payout_type")
     */
    private $type;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
    }


    /**
     * @param \Bml\AppBundle\Entity\Account $account
     */
    public function setAccount($account)
    {
        $this->account = $account;
    }

    /**
     * @return \Bml\AppBundle\Entity\Account
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param float $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param float $fee
     */
    public function setFee($fee)
    {
        $this->fee = $fee;
    }

    /**
     * @return float
     */
    public function getFee()
    {
        return $this->fee;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param boolean $paid
     */
    public function setPaid($paid)
    {
        $this->paid = $paid;
    }

    /**
     * @return boolean
     */
    public function getPaid()
    {
        return $this->paid;
    }

    /**
     * @return bool
     */
    public function getPartiallyPaid()
    {
        return $this->getPaid() && $this->getAmount() != $this->getExpectedAmount();
    }

    /**
     * @param \DateTime $paidOutTime
     */
    public function setPaidOutTime($paidOutTime)
    {
        $this->paidOutTime = $paidOutTime;
    }

    /**
     * @return \DateTime
     */
    public function getPaidOutTime()
    {
        return $this->paidOutTime;
    }

    /**
     * @param Round $round
     */
    public function setRound(Round $round)
    {
        $this->round = $round;
    }

    /**
     * @return Round
     */
    public function getRound()
    {
        return $this->round;
    }

    /**
     * @param \Bml\AppBundle\Entity\PayoutTx $tx
     */
    public function setTx($tx)
    {
        $this->tx = $tx;
    }

    /**
     * @return \Bml\AppBundle\Entity\PayoutTx
     */
    public function getTx()
    {
        return $this->tx;
    }

    /**
     * @return \Bml\AppBundle\Entity\Deposit
     */
    public function getDeposit()
    {
        return $this->deposit;
    }

    /**
     * @param \Bml\AppBundle\Entity\Deposit $deposit
     */
    public function setDeposit($deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * @param boolean $readyForPayout
     */
    public function setReadyForPayout($readyForPayout)
    {
        $this->readyForPayout = $readyForPayout;
    }

    /**
     * @return boolean
     */
    public function getReadyForPayout()
    {
        return $this->readyForPayout;
    }

    /**
     * @param int $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * @return bool
     */
    public function isReferrerPayout()
    {
        return $this->getType() == self::TYPE_REFERRER_PAYOUT;
    }

    /**
     * @return bool
     */
    public function isDefaultPayout()
    {
        return $this->getType() == self::TYPE_DEFAULT_PAYOUT;
    }

    /**
     * @return bool
     */
    public function isLastPayout()
    {
        return $this->getType() == self::TYPE_LAST_PAYOUT;
    }

    /**
     * @return bool
     */
    public function isRemainingFundsReturn()
    {
        return $this->getType() == self::TYPE_REMAINING_FUNDS_RETURN;
    }

    /**
     * @return bool
     */
    public function isLastAdminPayout()
    {
        return $this->getType() == self::TYPE_LAST_ADMIN_PAYOUT;
    }


    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param float $expectedAmount
     */
    public function setExpectedAmount($expectedAmount)
    {
        $this->expectedAmount = $expectedAmount;
    }

    /**
     * @return float
     */
    public function getExpectedAmount()
    {
        return $this->expectedAmount;
    }


}
