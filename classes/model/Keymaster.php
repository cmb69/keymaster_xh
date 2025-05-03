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

namespace Keymaster\Model;

class Keymaster
{
    /** @var string */
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    private function hasKey(): bool
    {
        return filesize($this->filename) > 0;
    }

    public function checkKey(string $key, int $now, int $duration): bool
    {
        $currentKey = file_get_contents($this->filename);
        if ($key === $currentKey) {
            $this->reset();
            return true;
        }
        if ($this->isFree($now, $duration)) {
            $this->newKey($key);
            return true;
        }
        return false;
    }

    public function revokeKey(string $key): void
    {
        $currentKey = file_get_contents($this->filename);
        if ($key === $currentKey) {
            file_put_contents($this->filename, "");
        }
    }

    public function claimKey(callable $genKey, int $now, int $duration): ?string
    {
        if (!$this->isFree($now, $duration)) {
            return null;
        }
        $key = $genKey();
        $this->newKey($key);
        return $key;
    }

    private function newKey(string $key): void
    {
        file_put_contents($this->filename, $key);
    }

    public function isFree(int $now, int $duration): bool
    {
        return !$this->hasKey() || $now - filemtime($this->filename) > $duration;
    }

    private function reset(): bool
    {
        return touch($this->filename);
    }
}
