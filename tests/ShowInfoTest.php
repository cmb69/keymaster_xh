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
use Keymaster\Infra\Request;
use Keymaster\Infra\SystemChecker;
use Keymaster\Infra\View;

class ShowInfoTest extends TestCase
{
    public function testRendersPluginInfoWithAllChecksSucceeding(): void
    {
        $sut = new ShowInfo("./plugins/keymaster/", $this->systemChecker(true), $this->view());
        $response = $sut($this->createStub(Request::class));
        Approvals::verifyHtml($response->output());
    }

    public function testRendersPluginInfoWithAllChecksFailing(): void
    {
        $sut = new ShowInfo("./plugins/keymaster/", $this->systemChecker(false), $this->view());
        $response = $sut($this->createStub(Request::class));
        Approvals::verifyHtml($response->output());
    }

    private function systemChecker(bool $returnValue): SystemChecker
    {
        $systemChecker = $this->createStub(SystemChecker::class);
        $systemChecker->method('checkVersion')->willReturn($returnValue);
        $systemChecker->method('checkExtension')->willReturn($returnValue);
        $systemChecker->method('checkWritability')->willReturn($returnValue);
        return $systemChecker;
    }

    private function view(): View
    {
        return new View("./templates/", XH_includeVar("./languages/en.php", "plugin_tx")['keymaster']);
    }
}
