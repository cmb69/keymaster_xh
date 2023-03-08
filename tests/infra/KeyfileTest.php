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

namespace Keymaster\Infra;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class KeyfileTest extends TestCase
{
    protected $filename;

    protected $keyfile;

    public function setUp(): void
    {
        vfsStream::setup("keyfile");
        $this->filename = "vfs://keyfile/key";
        file_put_contents($this->filename, "");

        $this->keyfile = new Keyfile($this->filename);
    }

    public function testFilename()
    {
        $expected = $this->filename;
        $actual = $this->keyfile->filename();
        $this->assertEquals($expected, $actual);
    }

    public function testMtimeIsNotInTheFuture()
    {
        $actual = $this->keyfile->mtime();
        $this->assertTrue($actual <= time());
    }

    public function testExtendIncreasesSizeByOne()
    {
        $expected = $this->keyfile->size() + 1;
        $actual = $this->keyfile->extend();
        $this->assertTrue($actual);
        clearstatcache();
        $actual = $this->keyfile->size();
        $this->assertEquals($expected, $actual);
    }

    public function testPurgeSetsFilesizeToZero()
    {
        $expected = 0;

        $actual = $this->keyfile->extend();
        $this->assertTrue($actual);

        $actual = $this->keyfile->purge();
        $this->assertTrue($actual);

        $actual = $this->keyfile->size();
        $this->assertEquals($expected, $actual);
    }
}
