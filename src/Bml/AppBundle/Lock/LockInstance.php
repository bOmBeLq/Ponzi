<?php


namespace Bml\AppBundle\Lock;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Lock
 */
class LockInstance
{

    /**
     * @var \resource
     */
    private $fp;

    /**
     * @param resource $fp
     */
    public function __construct($fp)
    {
        $this->fp = $fp;
    }


    public function unlock()
    {
        fclose($this->fp);
    }
}
