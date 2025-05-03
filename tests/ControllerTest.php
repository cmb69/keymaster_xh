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

use ApprovalTests\Approvals;
use Keymaster\Model\Model;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\FakeRequest;
use Plib\Random;
use Plib\View;

class ControllerTest extends TestCase
{
    /** @var Model&MockObject */
    private $model;

    /** @var Random&Stub */
    private $random;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        $this->model = $this->createMock(Model::class);
        $this->random = $this->createStub(Random::class);
        $this->view = new View("./templates/", XH_includeVar("./languages/en.php", "plugin_tx")["keymaster"]);
    }

    private function sut(): Controller
    {
        return new Controller($this->model, $this->random, $this->view);
    }

    public function testDoesNothingIfNothingNeedsToBeDone(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request);
        $this->assertEquals("", $response->output());
    }

    public function testRevokesKeyAfterLogout(): void
    {
        $this->model->expects($this->once())->method("revokeKey")->with("12345");
        $request = new FakeRequest([
            "url" => "http://example.com/?&logout",
            "cookie" => ["keymaster_key" => "12345"],
        ]);
        $response = $this->sut()($request);
        $this->assertSame("", $response->output());
    }

    public function testClaimsKey(): void
    {
        $this->model->expects($this->once())->method("claimKey")->willReturn("12345");
        $request = new FakeRequest(["admin" => true]);
        $response = $this->sut()($request);
        $this->assertSame(["keymaster_key", "12345", 0], $response->cookie());
    }

    public function testDoesNothingIfKeyIsAccepted(): void
    {
        $this->model->expects($this->once())->method("checkKey")->with("12345")->willReturn(true);
        $request = new FakeRequest(["admin" => true, "cookie" => ["keymaster_key" => "12345"]]);
        $response = $this->sut()($request);
        $this->assertEmpty($response->output());
    }

    public function testRendersLockingDialog(): void
    {
        $this->model->expects($this->once())->method("checkKey")->with("12346")->willReturn(false);
        $request = new FakeRequest([
            "admin" => true,
            "cookie" => ["keymaster_key" => "12346"],
        ]);
        $response = $this->sut()($request);
        $this->assertSame(409, $response->status());
        Approvals::verifyHtml($response->output());
    }
}
