<?php


namespace Bml\AppBundle\Twig;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Twig
 */
class CurrencyExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('currency', array($this, 'format')),
        );
    }

    /**
     * @param $value
     * @param bool $short
     * @return string
     */
    public function format($value, $short = true)
    {

        $originalValue = $value;
        $value = number_format($value, 8);
        $value = preg_replace('/(.*[^0]+)[0]+$/', '$1', $value);
        if(!$value) {
            return 0;
        }

        return $value;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bml_currency_format_extension';
    }


}
