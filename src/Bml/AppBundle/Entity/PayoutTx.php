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
class PayoutTx implements RoundedEntityInterface
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
     * @var Round
     *
     * @ORM\ManyToOne(targetEntity="Bml\AppBundle\Entity\Round")
     * @ORM\JoinColumn(nullable=false)
     */
    private $round;

    /**
     * @var float
     *
     * @ORM\Column(type="decimal", precision=60, scale=8, nullable=true)
     */
    private $txFee;


    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $txOut;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param float $txFee
     */
    public function setTxFee($txFee)
    {
        $this->txFee = $txFee;
    }

    /**
     * @return float
     */
    public function getTxFee()
    {
        return $this->txFee;
    }

    /**
     * @param string $txOut
     */
    public function setTxOut($txOut)
    {
        $this->txOut = $txOut;
    }

    /**
     * @return string
     */
    public function getTxOut()
    {
        return $this->txOut;
    }

    /**
     * @param Round $round
     */
    public function setRound(Round $round)
    {
        $this->round = $round;
    }

    /**
     * @return int
     */
    public function getRound()
    {
        return $this->round;
    }


}
