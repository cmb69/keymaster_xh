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

class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string,string> */
    private $lang;

    /**
     * @param array<string,string> $lang
     */
    public function __construct(string $templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    /** @param scalar $args */
    public function text(string $key, ...$args): string
    {
        return sprintf($this->lang[$key], ...$args);
    }

    /**
     * @param string $args
     */
    public function error(string $key, ...$args): string
    {
        return XH_message("fail", $this->lang[$key], ...$args);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data): string
    {
        extract($data);
        ob_start();
        include "{$this->templateFolder}{$template}.php";
        return (string) ob_get_clean();
    }
}
