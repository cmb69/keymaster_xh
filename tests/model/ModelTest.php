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

namespace Keymaster\Model;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    const NOW = 1678301321;

    public function testTellsCorrectFilename()
    {
        $filename = $this->keyfile("");
        $model = new Model($filename, 1678301321, 1800);
        $actual = $model->filename();
        $this->assertEquals($filename, $actual);
    }

    public function testIsFreeIfNotHasKey()
    {
        $filename = $this->keyfile("");
        $model = new Model($filename, self::NOW, 1800);
        $actual = $model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsFreeIfSessionHasExpired()
    {
        $filename = $this->keyfile("");
        $model = new Model($filename, self::NOW, 999);
        $actual = $model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsNotFreeWhenHasKeyAndSessionHasNotExpired()
    {
        $filename = $this->keyfile("12345");
        $model = new Model($filename, self::NOW, 1800);
        $actual = $model->isFree();
        $this->assertFalse($actual);
    }

    private function keyfile(bool $content): string
    {
        vfsStream::setup("root");
        $filename = "vfs://root/key";
        file_put_contents($filename, $content);
        touch($filename, self::NOW - 1000);
        return $filename;
    }
}
