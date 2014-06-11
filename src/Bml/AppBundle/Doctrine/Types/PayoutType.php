<?php


namespace Bml\AppBundle\Doctrine\Types;

use Bml\AppBundle\Entity\Payout;
use Doctrine\DBAL\Platforms\AbstractPlatform;


/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 */
class PayoutType extends AbstractEnumType
{
    protected $name;
    protected $values = [
        Payout::TYPE_DEFAULT_PAYOUT,
        Payout::TYPE_REFERRER_PAYOUT,
        Payout::TYPE_LAST_PAYOUT,
        Payout::TYPE_LAST_ADMIN_PAYOUT,
        Payout::TYPE_REMAINING_FUNDS_RETURN
    ];


} 