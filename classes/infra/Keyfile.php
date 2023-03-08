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

namespace Keymaster\Infra;

class Keyfile
{
    /** @var string*/
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function mtime(): int
    {
        return filemtime($this->filename);
    }

    public function touch(): bool
    {
        return touch($this->filename);
    }

    public function size(): int
    {
        return filesize($this->filename);
    }

    public function purge(): bool
    {
        $stream = fopen($this->filename, 'w');
        if ($stream) {
            return fclose($stream);
        } else {
            return false;
        }
    }

    public function extend(): bool
    {
        $stream = fopen($this->filename, 'a');
        if ($stream) {
            $ok = (fwrite($stream, '*') !== false);
            fclose($stream);
        } else {
            $ok = false;
        }
        return $ok;
    }
}
