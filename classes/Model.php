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

class Model
{
    /** @var Keyfile */
    private $keyfile;

    /** @var int */
    private $duration;

    public function __construct(Keyfile $keyfile, int $duration)
    {
        $this->keyfile = $keyfile;
        $this->duration = $duration;
    }

    public function filename(): string
    {
        return $this->keyfile->filename();
    }

    public function hasKey(): bool
    {
        return $this->keyfile->size() > 0;
    }

    public function isFree(): bool
    {
        return $this->hasKey() || $this->loggedInTime() > $this->duration;
    }

    public function loggedInTime(): int
    {
        return time() - $this->keyfile->mtime();
    }

    public function remainingTime(): int
    {
        return max($this->duration - $this->loggedInTime(), 0);
    }

    public function sessionHasExpired(): bool
    {
        return $this->remainingTime() <= 0;
    }

    public function reset(): bool
    {
        return $this->keyfile->touch();
    }

    public function give(): bool
    {
        return $this->keyfile->purge();
    }

    public function take(): bool
    {
        return $this->keyfile->extend();
    }

    /**
     * @return array<string,int>
     */
    public function jsConfig(): array
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
     * @return array<string,string>
     */
    public function jsL10n(): array
    {
        global $plugin_tx;

        return $plugin_tx['keymaster'];
    }
}
