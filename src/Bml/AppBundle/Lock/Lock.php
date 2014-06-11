<?php


namespace Bml\AppBundle\Lock;

/**
 * @author Damian Wróblewski <d.wroblewski@madden.pl>
 * @package Bml\AppBundle\Lock
 */
class Lock
{
    private static $locks = [];

    /**
     * @var string
     */
    private static $lockDir = '/tmp/';

    /**
     * @param $lockName
     * @param int $maxTime in seconds
     * @throws UnableToLockException
     * @return LockInstance
     */
    public static function lock($lockName, $maxTime = 0)
    {
        $file = self::$lockDir . '.lock_' . $lockName;

        $fp = fopen($file, "w+");

        do {
            if (flock($fp, LOCK_EX | LOCK_NB)) {
                $lock = self::$locks[] = new LockInstance($fp);
                return $lock;
            }
            sleep(1);
        } while ($maxTime-- > 0);
        throw new UnableToLockException('Unable to get lock "' . $lockName . '" (time ' . $maxTime . ')');
    }
}
