<?php

/**
 * The model class.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

/**
 * The model class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Keymaster_Model
{
    /**
     * The path of the lock file.
     *
     * var Keymaster_Keyfile
     */
    var $keyfile;

    /**
     * The maximum duration of a session in seconds.
     *
     * var int
     */
    var $duration;

    /**
     * Initializes a new instance.
     *
     * @param Keymaster_Keyfile $keyfile  A key file.
     * @param int               $duration Maximum duration of a session in seconds.
     */
    function Keymaster_Model(Keymaster_Keyfile $keyfile, $duration)
    {
        $this->keyfile = $keyfile;
        $this->duration = $duration;
    }

    /**
     * Returns the path of the lock file.
     *
     * @return string
     */
    function filename()
    {
        return $this->keyfile->filename();
    }

    /**
     * Returns whether the key is on the server.
     *
     * @return bool
     */
    function hasKey()
    {
        return $this->keyfile->size() > 0;
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
        return time() - $this->keyfile->mtime();
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
        return $this->keyfile->touch();
    }

    /**
     * Gives the key away and returns whether that succeeded.
     *
     * @return bool
     */
    function give()
    {
        return $this->keyfile->purge();
    }

    /**
     * Takes the key back and returns whether that succeeded.
     *
     * @return bool
     */
    function take()
    {
        return $this->keyfile->extend();
    }

    /**
     * Returns the JavaScript configuration.
     *
     * @return array
     *
     * @global array The configuration of the plugins.
     * @global array The localization of the plugins.
     */
    function jsConfig()
    {
        global $plugin_cf, $plugin_tx;

        $pcf = $plugin_cf['keymaster'];
        $ptx = $plugin_tx['keymaster'];
        $config = array(
            'warn' => $pcf['logout'] - $pcf['warn'],
            'pollInterval' => (int) $pcf['poll'],
            'text' => $ptx
        );
        return $config;
    }

    /**
     * Returns the path of the plugin icon.
     *
     * @return string
     *
     * @access public
     *
     * @global array The paths of system files and folders.
     */
    function pluginIconPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'keymaster/keymaster.png';
    }

    /**
     * Returns the path of a state icon.
     *
     * @param string $state A state.
     *
     * @return string
     *
     * @access public
     *
     * @global array The paths of system files and folders.
     */
    function stateIconPath($state)
    {
        global $pth;

        return $pth['folder']['plugins'] . 'keymaster/images/' . $state . '.png';
    }
}

?>
