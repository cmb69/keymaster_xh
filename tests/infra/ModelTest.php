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

use PHPUnit\Framework\TestCase;

class ModelTest extends TestCase
{
    protected $keyfile;

    protected $duration;

    public function setUp(): void
    {
        $this->keyfile = $this->createMock(Keyfile::class);
        $this->duration = 60;
        $this->model = new Model($this->keyfile, $this->duration);
    }

    public function testHasKeyAfterTaking()
    {
        $this->keyfile->expects($this->once())
            ->method('extend');
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(1));
        $this->model->take();
        $actual = $this->model->hasKey();
        $this->assertTrue($actual);
    }

    public function testNotHasKeyAfterGiving()
    {
        $this->keyfile->expects($this->once())
            ->method('purge');
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(0));
        $this->model->give();
        $actual = $this->model->hasKey();
        $this->assertFalse($actual);
    }

    public function testResetResetsLoggedInTime()
    {
        $this->keyfile->expects($this->once())
            ->method('touch');
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time()));
        $this->model->reset();
        $actual = $this->model->loggedInTime();
        $this->assertEquals(0, $actual);
    }

    public function testRemainingTimeIsDurationMinusLoggedInTime()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - 20));
        $expected = $this->duration - $this->model->loggedInTime();
        $actual = $this->model->remainingTime();
        $this->assertEquals($expected, $actual);
    }

    public function testSessionHasExpiredWhenRemainingTimeIsZero()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - $this->duration));
        $actual = $this->model->sessionHasExpired();
        $this->assertTrue($actual);
    }

    public function testFilenameEqualsKeyfilename()
    {
        $this->keyfile->expects($this->any())
            ->method('filename')
            ->will($this->returnValue('key'));
        $expected = $this->keyfile->filename();
        $actual = $this->model->filename();
        $this->assertEquals($expected, $actual);
    }

    public function testIsFreeIfHasKey()
    {
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(1));
        $actual = $this->model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsFreeIfSessionHasExpired()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - 2 * $this->duration));
        $actual = $this->model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsNotFreeWhenHasNotKeyAndSessionHasNotExpired()
    {
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(0));
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time()));
        $actual = $this->model->isFree();
        $this->assertFalse($actual);
    }

    public function testJsConfigHasRequiredElements()
    {
        global $plugin_cf;

        $plugin_cf = [
            'keymaster' => [
                'logout' => '1800',
                'warn' => '1200',
                'poll' => '7000',
            ]
        ];
        $actual = $this->model->jsConfig();
        $this->assertArrayHasKey('warn', $actual);
        $this->assertArrayHasKey('pollInterval', $actual);
    }

    public function testJsL10nIsPluginL10n()
    {
        global $plugin_tx;

        $plugin_tx = array(
            'keymaster' => array(
                'one' => '1',
                'two' => '2',
                'three' => '3'
            )
        );
        $expected = $plugin_tx['keymaster'];
        $actual = $this->model->jsL10n();
        $this->assertEquals($expected, $actual);
    }
}
