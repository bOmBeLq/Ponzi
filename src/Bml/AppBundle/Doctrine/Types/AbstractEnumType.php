<?php


namespace Bml\AppBundle\Doctrine\Types;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;


/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 */
abstract class AbstractEnumType extends Type
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string[]
     */
    protected $values = [];

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        $values = array_map(function ($val) {
            return "'" . $val . "'";
        }, $this->values);

        return "ENUM(" . implode(", ", $values) . ") COMMENT '(DC2Type:" . $this->name . ")'";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (!in_array($value, $this->values)) {
            throw new \InvalidArgumentException("Invalid '" . $this->name . "' value.");
        }
        return $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }



} 