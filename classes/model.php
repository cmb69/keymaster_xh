<?php

/**
 * Model class of Keymaster_XH.
 *
 * @package   Keymaster
 * @copyright Copyright (c) 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */


/**
 * The model of Keymaster_XH.
 *
 * @package Keymaster
 */
class Keymaster
{
    /**
     * The path of the lock file.
     *
     * var string
     */
    var $filename;


    /**
     * The maximum duration of a session in seconds.
     *
     * var int
     */
    var $duration;


    /**
     * Constructs a new Keymaster object.
     *
     * @param  string $filename  The path of the lock file.
     */
    function Keymaster($filename, $duration)
    {
        $this->filename = $filename;
        $this->duration = $duration;
    }


    /**
     * Returns the path of the lock file.
     *
     * return string
     */
    function getFilename()
    {
        return $this->filename;
    }


    /**
     * Returns whether the key is on the server.
     *
     * @return bool
     */
    function hasKey()
    {
        return filesize($this->filename) > 0;
    }


    /**
     * Returns whether the key is free to be given away.
     *
     * @return bool
     */
    function isFree()
    {
        return $this->hasKey() || $this->loggedInTime() > $this->duration;
    }


    /**
     * Returns the number of seconds the user is logged in.
     *
     * @return int
     */
    function loggedInTime()
    {
        assert(!$this->hasKey());
        return time() - filemtime($this->filename);
    }


    /**
     * Returns the number of seconds remaining for the session.
     *
     * @return int
     */
    function remainingTime()
    {
        return max($this->duration - $this->loggedInTime(), 0);
    }


    /**
     * Resets the logged in time and returns whether that succeeded.
     *
     * @return bool
     */
    function reset()
    {
        return touch($this->filename);
    }


    /**
     * Gives the key away and returns whether that succeeded.
     *
     * @return bool
     */
    function give()
    {
        $ok = ($fp = fopen($this->filename, 'w')) !== false;
        if ($fp !== false) {
            $ok = fclose($fp) && $ok;
        }
        return $ok;
    }


    /**
     * Takes the key back and returns whether that succeeded.
     *
     * @return bool
     */
    function take()
    {
        $ok = ($fp = fopen($this->filename, 'a')) !== false
            && fwrite($fp, '*') !== false;
        if ($fp !== false) {
            $ok = fclose($fp) && $ok;
        }
        return $ok;
    }
}

?>
