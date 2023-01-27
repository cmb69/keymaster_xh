<?php

/**
 * Copyright 2023 Christoph M. Becker
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

class Response
{
    /** @var string */
    private $output;

    /** @var string */
    private $hjs;

    /** @var string */
    private $bjs;

    public function __construct(string $output, string $hjs = "", string $bjs = "")
    {
        $this->output = $output;
        $this->hjs = $hjs;
        $this->bjs = $bjs;
    }

    public function process(): string
    {
        global $hjs, $bjs;

        $hjs .= $this->hjs;
        $bjs .= $this->bjs;
        return $this->output;
    }

    public function representation(): string
    {
        return print_r($this, true);
    }
}
