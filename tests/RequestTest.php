<?php

/**
 * Copyright (C) 2022 Christoph M. Becker
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
use Keymaster\Infra\Request;

class RequestTest extends TestCase
{
    public function testIsAdmin(): void
    {
        global $adm;

        $request = new Request();
        $adm = false;
        $this->assertFalse($request->isAdmin());
        $adm = true;
        $this->assertTrue($request->isAdmin());
    }

    public function testWantsLogin(): void
    {
        global $f;

        $request = new Request();
        $this->assertFalse($request->wantsLogin());
        $f = "login";
        $this->assertTrue($request->wantsLogin());
    }

    public function testIsLogout(): void
    {
        global $f;

        $request = new Request();
        $this->assertFalse($request->isLogout());
        $f = "xh_loggedout";
        $this->assertTrue($request->isLogout());
    }
}
