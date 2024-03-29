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

namespace Keymaster;

use Keymaster\Infra\Model;
use Keymaster\Infra\Request;
use Keymaster\Infra\Response;
use Keymaster\Infra\View;

class EmitScripts
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(string $pluginFolder, array $conf, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        if ($request->adm()) {
            $config = json_encode(
                $this->jsConf(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs = "<meta name='keymaster_config' content='$config'>";
            $l10n = json_encode(
                $this->view->jsTexts(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs .= "<meta name='keymaster_lang' content='$l10n'>";
            $filename = "{$this->pluginFolder}keymaster.min.js";
            $hjs .= $this->js($filename);
            $bjs = $this->view->render("dialog", []);
            return Response::create("")->withHjs($hjs)->withBjs($bjs);
        }
        return Response::create("");
    }

    /** @return array{warn:int,pollInterval:int} */
    private function jsConf(): array
    {
        return [
            "warn" => (int) $this->conf["logout"] - (int) $this->conf["warn"],
            "pollInterval" => (int) $this->conf["poll"]
        ];
    }

    private function js(string $filename): string
    {
        return <<<EOT
<script type="module" src="$filename"></script>
EOT;
    }
}
