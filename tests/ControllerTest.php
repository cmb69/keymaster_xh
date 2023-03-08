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
use Keymaster\Infra\Model;
use Keymaster\Infra\Request;
use Keymaster\Infra\View;
use PHPUnit\Framework\TestCase;

class ControllerTest extends TestCase
{
    public function testDeniesLoginIfNotFree(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("isFree")->willReturn(false);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("wantsLogin")->willReturn(true);
        $response = $sut($request);

        $this->assertTrue($response->clearedF());
        Approvals::verifyHtml($response->output());
    }

    public function testLoginSucceedsIfFreeAndKeyIsGiven(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("isFree")->willReturn(true);
        $model->expects($this->once())->method("give")->willReturn(true);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isLogin")->willReturn(true);
        $response = $sut($request);

        $this->assertEquals("", $response->output());
    }

    public function testLoginWarnsIfKeyIsNotGivenEvenIfFree(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("isFree")->willReturn(true);
        $model->expects($this->once())->method("give")->willReturn(false);
        $model->method("filename")->willReturn("./plugins/keymaster/key");
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isLogin")->willReturn(true);
        $response = $sut($request);

        Approvals::verifyHtml($response->output());
    }

    public function testLoginFailsIfNotFree(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("isFree")->willReturn(false);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isLogin")->willReturn(true);
        $request->method("su")->willReturn("Current_Page");
        $response = $sut($request);

        $this->assertTrue($response->logout());
        $this->assertEquals("http://example.com/?Current_Page", $response->location());
    }

    public function testLogoutSucceedsIfKeyCanBeTaken(): void
    {
        $model = $this->createMock(Model::class);
        $model->expects($this->once())->method("take")->willReturn(true);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isLogout")->willReturn(true);
        $response = $sut($request);

        $this->assertEquals("", $response->output());
    }

    public function testLogoutWarnsIfKeyCannotBeTaken(): void
    {
        $model = $this->createMock(Model::class);
        $model->expects($this->once())->method("take")->willReturn(false);
        $model->method("filename")->willReturn("./plugins/keymaster/key");
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isLogout")->willReturn(true);
        $response = $sut($request);

        Approvals::verifyHtml($response->output());
    }

    public function testAnswersRemainingTimeIfRequestedByAdmin(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("remainingTime")->willReturn(600);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isTimeRequested")->willReturn(true);
        $request->method("isAdmin")->willReturn(true);
        $response = $sut($request);

        $this->assertEquals("text/plain", $response->contentType());
        $this->assertEquals("600", $response->output());
    }

    public function testAnswersThatNoTimeIsRemainingIfRequestedByVisitor(): void
    {
        $model = $this->createMock(Model::class);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isTimeRequested")->willReturn(true);
        $request->method("isAdmin")->willReturn(false);
        $response = $sut($request);

        $this->assertEquals("text/plain", $response->contentType());
        $this->assertEquals("-1", $response->output());
    }


    public function testResetsTheTimeIfRequestedByAdmin(): void
    {
        $model = $this->createMock(Model::class);
        $model->expects($this->once())->method("reset")->willReturn(true);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isAdmin")->willReturn(true);
        $request->method("isResetRequested")->willReturn(true);
        $response = $sut($request);

        $this->assertEquals("text/plain", $response->contentType());
        $this->assertEquals("1", $response->output());
    }

    public function testResetsTheTimeOnAdminRequestIfSessionIsNotExpired(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("sessionHasExpired")->willReturn(false);
        $model->expects($this->once())->method("reset")->willReturn(true);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isAdmin")->willReturn(true);
        $response = $sut($request);

        $this->assertEquals("", $response->output());
    }

    public function testWarnsIfResettingTimeFailsEvenIfSessionIsNotExpired(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("sessionHasExpired")->willReturn(false);
        $model->expects($this->once())->method("reset")->willReturn(false);
        $model->method("filename")->willReturn("./plugins/keymaster/key");
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isAdmin")->willReturn(true);
        $response = $sut($request);

        Approvals::verifyHtml($response->output());
    }

    public function testLogsOutOnAdminRequestIfSessionIsExpired(): void
    {
        $model = $this->createMock(Model::class);
        $model->method("sessionHasExpired")->willReturn(true);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $request->method("isAdmin")->willReturn(true);
        $request->method("su")->willReturn("Current_Page");
        $response = $sut($request);

        $this->assertTrue($response->logout());
        $this->assertEquals("http://example.com/?Current_Page", $response->location());
    }

    public function testReturnsEmptyResponseOtherwise(): void
    {
        $model = $this->createMock(Model::class);
        $sut = new Controller($model, $this->view());

        $request = $this->createStub(Request::class);
        $response = $sut($request);

        $this->assertEquals("", $response->output());
    }

    private function view(): View
    {
        return new View("./templates/", XH_includeVar("./languages/en.php", "plugin_tx")["keymaster"]);
    }
}
