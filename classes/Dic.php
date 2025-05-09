<?php

/**
 * Copyright (C) 2023 Christoph M. Becker
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

use Plib\DocumentStore;
use Plib\Random;
use Plib\SystemChecker;
use Plib\View;

class Dic
{
    public static function gatekeeper(): Gatekeeper
    {
        global $pth, $plugin_cf;
        return new Gatekeeper(
            $pth["folder"]["plugins"] . "keymaster/",
            $plugin_cf["keymaster"],
            self::store(),
            new Random(),
            self::view()
        );
    }

    public static function makeShowInfo(): ShowInfo
    {
        global $pth;
        return new ShowInfo(
            $pth["folder"]["plugins"] . "keymaster/",
            self::store(),
            new SystemChecker(),
            self::view()
        );
    }

    private static function store(): DocumentStore
    {
        global $pth;
        return new DocumentStore($pth["folder"]["plugins"] . "keymaster/");
    }

    private static function view(): View
    {
        global $pth, $plugin_tx;
        return new View($pth["folder"]["plugins"] . "keymaster/views/", $plugin_tx["keymaster"]);
    }
}
