<?php

/**
 * Copyright (C) 2013-2023 Christoph M. Becker
 *
 * This file is part of Keymaster_XH.
 *
 * Keymaster_XH is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Keymaster_XH is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Keymaster_XH.  If not, see <http://www.gnu.org/licenses/>.
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
