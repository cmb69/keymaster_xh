<?php

/**
 * The model class.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
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
     * @var Keymaster_Keyfile
     *
     * @access private
     */
    var $_keyfile;

    /**
     * The maximum duration of a session in seconds.
     *
     * @var int
     *
     * @access private
     */
    var $_duration;

    /**
     * Initializes a new instance.
     *
     * @param Keymaster_Keyfile $keyfile  A key file.
     * @param int               $duration Maximum duration of a session in seconds.
     *
     * @return void
     *
     * @access public
     */
    function Keymaster_Model(Keymaster_Keyfile $keyfile, $duration)
    {
        $this->_keyfile = $keyfile;
        $this->_duration = $duration;
    }

    /**
     * Returns the path of the lock file.
     *
     * @return string
     *
     * @access public
     */
    function filename()
    {
        return $this->_keyfile->filename();
    }

    /**
     * Returns whether the key is on the server.
     *
     * @return bool
     *
     * @access public
     */
    function hasKey()
    {
        return $this->_keyfile->size() > 0;
    }

    /**
     * Returns whether the key is free to be given away.
     *
     * @return bool
     *
     * @access public
     */
    function isFree()
    {
        return $this->hasKey() || $this->loggedInTime() > $this->_duration;
    }

    /**
     * Returns the number of seconds the user is logged in.
     *
     * @return int
     *
     * @access public
     */
    function loggedInTime()
    {
        return time() - $this->_keyfile->mtime();
    }

    /**
     * Returns the number of seconds remaining for the session.
     *
     * @return int
     *
     * @access public
     */
    function remainingTime()
    {
        return max($this->_duration - $this->loggedInTime(), 0);
    }

    /**
     * Returns whether the session has expired.
     *
     * @return bool
     *
     * @access public
     */
    function sessionHasExpired()
    {
        return $this->remainingTime() <= 0;
    }

    /**
     * Resets the logged in time and returns whether that succeeded.
     *
     * @return bool
     *
     * @access public
     */
    function reset()
    {
        return $this->_keyfile->touch();
    }

    /**
     * Gives the key away and returns whether that succeeded.
     *
     * @return bool
     *
     * @access public
     */
    function give()
    {
        return $this->_keyfile->purge();
    }

    /**
     * Takes the key back and returns whether that succeeded.
     *
     * @return bool
     *
     * @access public
     */
    function take()
    {
        return $this->_keyfile->extend();
    }

    /**
     * Returns the JavaScript configuration.
     *
     * @return array
     *
     * @access public
     *
     * @global array The configuration of the plugins.
     */
    function jsConfig()
    {
        global $plugin_cf;

        $pcf = $plugin_cf['keymaster'];
        $config = array(
            'warn' => $pcf['logout'] - $pcf['warn'],
            'pollInterval' => (int) $pcf['poll']
        );
        return $config;
    }

    /**
     * Returns the JavaScript localization.
     *
     * @return array
     *
     * @access public
     *
     * @global array The localization of the plugins.
     */
    function jsL10n()
    {
        global $plugin_tx;

        return $plugin_tx['keymaster'];
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
