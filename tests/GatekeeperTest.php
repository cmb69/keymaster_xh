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
use Keymaster\Model\Keymaster;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\Random;
use Plib\View;

class GatekeeperTest extends TestCase
{
    /** @var DocumentStore */
    private $store;

    /** @var Random&Stub */
    private $random;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $keymaster = Keymaster::updateIn($this->store);
        $keymaster->set("12345", strtotime("2025-05-03T16:14:20+00:00"));
        $this->store->commit();
        $this->random = $this->createStub(Random::class);
        $this->random->method("bytes")->willReturn("123456789ABCDEF");
        $this->view = new View("./templates/", XH_includeVar("./languages/en.php", "plugin_tx")["keymaster"]);
    }

    private function sut(): Gatekeeper
    {
        return new Gatekeeper(
            "./plugins/keymaster/",
            XH_includeVar("./config/config.php", "plugin_cf")["keymaster"],
            $this->store,
            $this->random,
            $this->view
        );
    }

    public function testDoesNothingIfNothingNeedsToBeDone(): void
    {
        $request = new FakeRequest();
        $response = $this->sut()($request);
        $this->assertEquals("", $response->output());
    }

    public function testRevokesKeyAfterLogout(): void
    {
        $request = new FakeRequest([
            "url" => "http://example.com/?&logout",
            "cookie" => ["keymaster_key" => "12345"],
        ]);
        $response = $this->sut()($request);
        $this->assertSame("", $response->output());
    }

    public function testReportsFailureToRevokeKeyAfterLogout(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "url" => "http://example.com/?&logout",
            "cookie" => ["keymaster_key" => "12345"],
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("The key cannot be saved!", $response->output());
    }

    public function testAcceptsValidKey(): void
    {
        $request = new FakeRequest(["admin" => true, "cookie" => ["keymaster_key" => "12345"]]);
        $response = $this->sut()($request);
        $this->assertEmpty($response->output());
    }

    public function testReportsFailureToAcceptKey(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest(["admin" => true, "cookie" => ["keymaster_key" => "12345"]]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("The key cannot be saved!", $response->output());
    }

    public function testGrantsKey(): void
    {
        $request = new FakeRequest([
            "admin" => true,
            "time" => strtotime("2025-05-03T17:14:20+00:00"),
        ]);
        $response = $this->sut()($request);
        $this->assertSame(["keymaster_key", "64P36D1L6ORJGEA1891K8HA6", 0], $response->cookie());
    }

    public function testReportsFailureToGrantKey(): void
    {
        vfsStream::setQuota(0);
        $request = new FakeRequest([
            "admin" => true,
            "time" => strtotime("2025-05-03T17:14:20+00:00"),
        ]);
        $response = $this->sut()($request);
        $this->assertStringContainsString("The key cannot be saved!", $response->output());
    }

    public function testRendersLockScreen(): void
    {
        $request = new FakeRequest([
            "admin" => true,
            "cookie" => ["keymaster_key" => "12346"],
            "time" => strtotime("2025-05-03T16:14:24+00:00"),
        ]);
        $response = $this->sut()($request);
        $this->assertSame(409, $response->status());
        Approvals::verifyHtml($response->output());
    }
}
