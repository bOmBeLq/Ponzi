<?php


namespace Bml\AppBundle\Twig;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Twig
 */
class TimeAgoExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('timeAgo', array($this, 'format')),
        );
    }


    /**
     * @param \DateTime $date
     * @return string
     */
    public function format(\DateTime $date = null)
    {
        if(!$date) {
            return '-';
        }
        $now = new \DateTime();

        $diff = $now->diff($date);

        if($diff -> y) {
            return $diff -> y.' year(s)';
        }
        if($diff -> m) {
            return $diff -> m.' month(s)';
        }
        if($diff -> d) {
            return $diff -> d.' day(s)';
        }
        if($diff -> h) {
            return $diff -> h.' hour(s)';
        }
        if($diff -> i) {
            return $diff -> i.' min.';
        }
        if($diff -> s) {
            return $diff -> s.' sec.';
        }


    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'bml_time_ago_extension';
    }


}
