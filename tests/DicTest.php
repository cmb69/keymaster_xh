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

use PHPUnit\Framework\TestCase;

class DicTest extends TestCase
{
    public function setUp(): void
    {
        global $pth, $plugin_cf, $plugin_tx;

        $pth = ["folder" => ["plugins" => ""]];
        $plugin_cf = ["keymaster" => ["logout" => ""]];
        $plugin_tx = ["keymaster" => []];
    }

    public function testMakesController(): void
    {
        $this->assertInstanceOf(Controller::class, Dic::makeController());
    }

    public function testMakesEmitScripts(): void
    {
        $this->assertInstanceOf(EmitScripts::class, Dic::makeEmitScripts());
    }

    public function testMakesShowInfo(): void
    {
        $this->assertInstanceOf(ShowInfo::class, Dic::makeShowInfo());
    }
}
