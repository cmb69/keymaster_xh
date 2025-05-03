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
use org\bovigo\vfs\vfsStream;
use Plib\DocumentStore;
use Plib\FakeRequest;
use Plib\FakeSystemChecker;
use Plib\View;

class ShowInfoTest extends TestCase
{
    /** @var DocumentStore */
    private $store;

    /** @var FakeSystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function setUp(): void
    {
        vfsStream::setup("root");
        $this->store = new DocumentStore(vfsStream::url("root/"));
        $this->systemChecker = new FakeSystemChecker();
        $this->view = new View("./templates/", XH_includeVar("./languages/en.php", "plugin_tx")['keymaster']);
    }

    private function sut(): ShowInfo
    {
        return new ShowInfo(
            "./plugins/keymaster/",
            $this->store,
            $this->systemChecker,
            $this->view
        );
    }

    public function testRendersPluginInfoWithAllChecksSucceeding(): void
    {
        $this->systemChecker = new FakeSystemChecker(true);
        $response = $this->sut()(new FakeRequest());
        Approvals::verifyHtml($response->output());
    }

    public function testRendersPluginInfoWithAllChecksFailing(): void
    {
        $response = $this->sut()(new FakeRequest());
        Approvals::verifyHtml($response->output());
    }
}
