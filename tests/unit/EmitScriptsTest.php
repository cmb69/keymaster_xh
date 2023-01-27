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
use Keymaster\Infra\Request;
use ApprovalTests\Approvals;

class EmitScriptsTest extends TestCase
{
    public function testEmitsJavaScript(): void
    {
        $request = $this->createStub(Request::class);
        $request->method('isAdmin')->willReturn(true);
        $model = $this->createStub(Model::class);
        $model->method('jsConfig')->willReturn(['warn' => 600, 'pollInterval' => 7000]);
        $plugin_tx = XH_includeVar("./languages/en.php", "plugin_tx");
        $model->method('jsL10n')->willReturn($plugin_tx['keymaster']);

        $subject = new EmitScripts(
            "./",
            $request,
            $model,
            $plugin_tx['keymaster']
        );
        $response = $subject();

        Approvals::verifyHtml($response->representation());
    }
}
