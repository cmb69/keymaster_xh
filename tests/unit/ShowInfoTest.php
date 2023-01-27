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
use ApprovalTests\Approvals;

class ShowInfoTest extends TestCase
{
    public function testRendersPluginInfo(): void
    {
        $systemChecker = $this->createStub(SystemChecker::class);
        $systemChecker->method('checkVersion')->willReturn(true);
        $systemChecker->method('checkExtension')->willReturn(true);
        $systemChecker->method('checkWritability')->willReturn(true);
        $subject = new ShowInfo(
            "./",
            XH_includeVar("./languages/en.php", "plugin_tx")['keymaster'],
            $systemChecker
        );

        $response = $subject();

        Approvals::verifyString($response->representation());
    }
}
