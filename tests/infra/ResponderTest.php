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

namespace Keymaster\Infra;

use PHPUnit\Framework\TestCase;

class ResponderTest extends TestCase
{
    public function testLogout(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("setcookie")->with(
            $this->equalTo("status"),
            $this->equalTo(""),
            $this->equalTo(0),
            $this->equalTo(CMSIMPLE_ROOT)
        );
        $sut->expects($this->once())->method("unsetSessionPassword");

        $response = Response::create("")->withLogout();
        $sut->doRespond($response);
    }

    public function testLocation(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("purgeOutputBuffers");
        $sut->expects($this->once())->method("header")->with(
            $this->equalTo("Location: http://example.com/"),
            $this->equalTo(true),
            $this->equalTo(303)
        );
        $sut->expects($this->once())->method("exit");

        $response = Response::redirect("http://example.com/");
        $sut->doRespond($response);
    }

    public function testContentType(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("purgeOutputBuffers");
        $sut->expects($this->once())->method("header")->with($this->equalTo("Content-Type: text/plain"));
        $sut->expects($this->once())->method("print")->with($this->equalTo("some output"));
        $sut->expects($this->once())->method("exit");

        $response = Response::create("some output")->withContentType("text/plain");
        $sut->doRespond($response);
    }

    public function testHjs(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("hjs")->with($this->equalTo("some hjs"));

        $response = Response::create("")->withHjs("some hjs");
        $sut->doRespond($response);
    }

    public function testBjs(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("bjs")->with($this->equalTo("some bjs"));

        $response = Response::create("")->withBjs("some bjs");
        $sut->doRespond($response);
    }

    public function testClearsF(): void
    {
        $sut = $this->sut();
        $sut->expects($this->once())->method("f")->with($this->equalTo(""));

        $response = Response::create("")->withClearedF();
        $sut->doRespond($response);
    }

    private function sut(): Responder
    {
        return $this->getMockBuilder(Responder::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->onlyMethods(["bjs", "exit", "f", "header", "hjs", "print", "purgeOutputBuffers", "setcookie", "unsetSessionPassword"])
            ->getMock();
    }
}
