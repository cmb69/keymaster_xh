<?php

/**
 * Copyright (C) 2022-2023 Christoph M. Becker
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

use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase
{
    /** @dataProvider wantsLoginData */
    public function testWantsLogin(string $login, bool $expected): void
    {
        $sut = $this->sut();
        $sut->method("f")->willReturn($login);

        $actual = $sut->wantsLogin();

        $this->assertSame($expected, $actual);
    }

    public function wantsLoginData(): array
    {
        return [
            ["", false],
            ["login", true],
        ];
    }

    /** @dataProvider isLoginData */
    public function testIsLogin(string $login, array $cookies, bool $check, bool $expected): void
    {
        $sut = $this->sut();
        $sut->method("login")->willReturn($login);
        $sut->method("cookie")->willReturn($cookies);
        $sut->method("logincheck")->willReturn($check);

        $result = $sut->isLogin();

        $this->assertSame($expected, $result);
    }

    public function isLoginData(): array
    {
        return [
            ["true", ["status" => "adm"], false, true],
            ["", ["status" => "adm"], false, false],
        ];
    }

    /** @dataProvider isLogoutData */
    public function testIsLogout(string $login, bool $expected): void
    {
        $sut = $this->sut();
        $sut->method("f")->willReturn($login);

        $actual = $sut->isLogout();

        $this->assertSame($expected, $actual);
    }

    public function isLogoutData(): array
    {
        return [
            ["", false],
            ["xh_loggedout", true],
        ];
    }

    /** @dataProvider isTimeRequestedData */
    public function testIsTimeRequested(array $get, bool $expected): void
    {
        $sut = $this->sut();
        $sut->method("get")->willReturn($get);

        $actual = $sut->isTimeRequested();

        $this->assertSame($expected, $actual);
    }

    public function isTimeRequestedData(): array
    {
        return [
            [["keymaster_time" => ""], true],
            [[], false],
        ];
    }

    /** @dataProvider isResetRequestedData */
    public function testIsResetRequested(array $post, bool $expected): void
    {
        $sut = $this->sut();
        $sut->method("post")->willReturn($post);

        $actual = $sut->isResetRequested();

        $this->assertSame($expected, $actual);
    }

    public function isResetRequestedData(): array
    {
        return [
            [["keymaster_reset" => ""], true],
            [[], false],
        ];
    }

    private function sut(): Request
    {
        return $this->getMockBuilder(Request::class)
        ->disableOriginalConstructor()
        ->disableOriginalClone()
        ->disableArgumentCloning()
        ->disallowMockingUnknownTypes()
        ->onlyMethods(["adm", "cookie", "f", "get", "login", "logincheck", "post", "su"])
        ->getMock();
    }
}
