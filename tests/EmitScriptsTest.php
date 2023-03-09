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

use function XH_includeVar;
use PHPUnit\Framework\TestCase;
use Keymaster\Infra\Request;
use ApprovalTests\Approvals;
use Keymaster\Infra\Model;
use Keymaster\Infra\View;

class EmitScriptsTest extends TestCase
{
    public function testEmitsHjs(): void
    {
        $sut = new EmitScripts("./plugins/keymaster/", $this->conf(), $this->view());

        $request = $this->createStub(Request::class);
        $request->method('adm')->willReturn(true);
        $response = $sut($request);

        Approvals::verifyHtml($response->hjs());
    }

    public function testEmitsBjs(): void
    {
        $sut = new EmitScripts("./plugins/keymaster/", $this->conf(), $this->view());

        $request = $this->createStub(Request::class);
        $request->method('adm')->willReturn(true);
        $response = $sut($request);

        Approvals::verifyHtml($response->bjs());
    }

    public function testDoesNothingIfNotAdmin(): void
    {
        $sut = new EmitScripts("./plugins/keymaster/", $this->conf(), $this->view());

        $request = $this->createStub(Request::class);
        $request->method('adm')->willReturn(false);
        $response = $sut($request);

        $this->assertEquals("", $response->hjs());
        $this->assertEquals("", $response->bjs());
    }

    private function view(): View
    {
        return new View("./templates/", $this->text());
    }

    private function conf(): array
    {
        return XH_includeVar("./config/config.php", "plugin_cf")["keymaster"];
    }

    private function text(): array
    {
        return XH_includeVar("./languages/en.php", "plugin_tx")["keymaster"];
    }
}
