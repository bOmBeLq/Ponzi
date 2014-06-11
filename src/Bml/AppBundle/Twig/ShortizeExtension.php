<?php


namespace Bml\AppBundle\Twig;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Twig
 */
class ShortizeExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('shortize', array($this, 'format')),
        );
    }


    /**
     * @param string $string
     * @param int $maxLength
     * @return string
     */
    public function format($string, $maxLength = 15)
    {
        if (strlen($string) <= $maxLength) {
            return $string;
        }
        return substr($string, 0, 6) . '...' . substr($string, -6);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bml_shortize_extension';
    }


}
