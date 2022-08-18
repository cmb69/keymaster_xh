<?php

/**
 * The model class.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2019 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

namespace Keymaster;

/**
 * The model class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Model
{
    /**
     * The path of the lock file.
     *
     * @var Keyfile
     */
    private $keyfile;

    /**
     * The maximum duration of a session in seconds.
     *
     * @var int
     */
    private $duration;

    /**
     * Initializes a new instance.
     *
     * @param Keyfile $keyfile  A key file.
     * @param int     $duration Maximum duration of a session in seconds.
     *
     * @return void
     */
    public function __construct(Keyfile $keyfile, $duration)
    {
        $this->keyfile = $keyfile;
        $this->duration = $duration;
    }

    /**
     * Returns the path of the lock file.
     *
     * @return string
     */
    public function filename()
    {
        return $this->keyfile->filename();
    }

    /**
     * Returns whether the key is on the server.
     *
     * @return bool
     */
    public function hasKey()
    {
        return $this->keyfile->size() > 0;
    }

    /**
     * Returns whether the key is free to be given away.
     *
     * @return bool
     */
    public function isFree()
    {
        return $this->hasKey() || $this->loggedInTime() > $this->duration;
    }

    /**
     * Returns the number of seconds the user is logged in.
     *
     * @return int
     */
    public function loggedInTime()
    {
        return time() - $this->keyfile->mtime();
    }

    /**
     * Returns the number of seconds remaining for the session.
     *
     * @return int
     */
    public function remainingTime()
    {
        return max($this->duration - $this->loggedInTime(), 0);
    }

    /**
     * Returns whether the session has expired.
     *
     * @return bool
     */
    public function sessionHasExpired()
    {
        return $this->remainingTime() <= 0;
    }

    /**
     * Resets the logged in time and returns whether that succeeded.
     *
     * @return bool
     */
    public function reset()
    {
        return $this->keyfile->touch();
    }

    /**
     * Gives the key away and returns whether that succeeded.
     *
     * @return bool
     */
    public function give()
    {
        return $this->keyfile->purge();
    }

    /**
     * Takes the key back and returns whether that succeeded.
     *
     * @return bool
     */
    public function take()
    {
        return $this->keyfile->extend();
    }

    /**
     * Returns the JavaScript configuration.
     *
     * @return array
     *
     * @global array The configuration of the plugins.
     */
    public function jsConfig()
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
     * @global array The localization of the plugins.
     */
    public function jsL10n()
    {
        global $plugin_tx;

        return $plugin_tx['keymaster'];
    }

    /**
     * Returns the path of the plugin icon.
     *
     * @return string
     *
     * @global array The paths of system files and folders.
     */
    public function pluginIconPath()
    {
        global $pth;

        return $pth['folder']['plugins'] . 'keymaster/keymaster.png';
    }
}
