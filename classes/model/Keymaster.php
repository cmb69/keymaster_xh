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

use Plib\Document;
use Plib\DocumentStore;

final class Keymaster implements Document
{
    /** @var string */
    private $key;

    /** @var int */
    private $timestamp;

    public static function updateIn(DocumentStore $store): self
    {
        $that = $store->update("key", self::class);
        assert($that instanceof self);
        return $that;
    }

    public static function fromString(string $contents, string $key): self
    {
        $record = explode(",", rtrim($contents), 2);
        return new self($record[0], count($record) === 2 ? (int) $record[1] : 0);
    }

    public function __construct(string $key, int $timestamp)
    {
        $this->key = $key;
        $this->timestamp = $timestamp;
    }

    public function toString(): string
    {
        return $this->key . "," . $this->timestamp;
    }

    private function hasKey(): bool
    {
        return $this->key !== "";
    }

    public function acceptKey(string $key, int $now, int $duration): bool
    {
        if ($key === $this->key) {
            $this->timestamp = $now;
            return true;
        }
        if ($this->isFree($now, $duration)) {
            $this->key = $key;
            $this->timestamp = $now;
            return true;
        }
        return false;
    }

    public function grantKey(callable $genKey, int $now, int $duration): ?string
    {
        if (!$this->isFree($now, $duration)) {
            return null;
        }
        $this->key = $genKey();
        return $this->key;
    }

    public function revokeKey(string $key): void
    {
        if ($this->key === $key) {
            $this->key = "";
            $this->timestamp = 0;
        }
    }

    public function isFree(int $now, int $duration): bool
    {
        return !$this->hasKey() || $now - $this->timestamp > $duration;
    }

    public function set(string $key, int $timestamp): void
    {
        $this->key = $key;
        $this->timestamp = $timestamp;
    }
}
