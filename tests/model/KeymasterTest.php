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

use PHPUnit\Framework\TestCase;

/** @small */
class KeymasterTest extends TestCase
{
    public function testAcceptsCorrectKey(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:50:30+00:00"));
        $actual = $keymaster->acceptKey("12345", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertTrue($actual);
        $this->assertEquals(new Keymaster("12345", strtotime("2025-05-03T16:51:30+00:00")), $keymaster);
    }

    public function testAcceptsNewKey(): void
    {
        $keymaster = new Keymaster("", strtotime("2025-05-03T16:50:30+00:00"));
        $actual = $keymaster->acceptKey("12345", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertTrue($actual);
        $this->assertEquals(new Keymaster("12345", strtotime("2025-05-03T16:51:30+00:00")), $keymaster);
    }

    public function testAcceptsOtherKeyIfExpired(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:29+00:00"));
        $actual = $keymaster->acceptKey("67890", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertTrue($actual);
        $this->assertEquals(new Keymaster("67890", strtotime("2025-05-03T16:51:30+00:00")), $keymaster);
    }

    public function testRejectsOtherKeyIfNotExpired(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00"));
        $actual = $keymaster->acceptKey("67890", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertFalse($actual);
        $this->assertEquals(new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00")), $keymaster);
    }

    public function testDoesNotGrantKeyIfNotExpired(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00"));
        $actual = $keymaster->grantKey(fn () => "67890", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertNull($actual);
        $this->assertEquals(new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00")), $keymaster);
    }

    public function testGrantsKeyIfExpired(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:29+00:00"));
        $actual = $keymaster->grantKey(fn () => "67890", strtotime("2025-05-03T16:51:30+00:00"), 1800);
        $this->assertSame("67890", $actual);
        $this->assertEquals(new Keymaster("67890", strtotime("2025-05-03T16:51:30+00:00")), $keymaster);
    }

    public function testRevokesSameKey(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00"));
        $keymaster->revokeKey("12345");
        $this->assertEquals(new Keymaster("", 0), $keymaster);
    }

    public function testDoesNotRevokeOtherKey(): void
    {
        $keymaster = new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00"));
        $keymaster->revokeKey("67890");
        $this->assertEquals(new Keymaster("12345", strtotime("2025-05-03T16:21:30+00:00")), $keymaster);
    }
}
