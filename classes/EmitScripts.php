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

use Keymaster\Infra\Request;

class EmitScripts
{
    /** @var string */
    private $pluginFolder;

    /** @var Request */
    private $request;

    /** @var Model */
    private $model;

    /** @var array<string> */
    private $lang;

    /** @var View */
    private $view;

    /**
     * @param array<string> $lang
     */
    public function __construct(string $pluginFolder, Request $request, Model $model, array $lang)
    {
        $this->pluginFolder = $pluginFolder;
        $this->request = $request;
        $this->model = $model;
        $this->lang = $lang;
        $this->view = new View("{$this->pluginFolder}templates/", $lang);
    }

    public function __invoke(): Response
    {
        if ($this->request->isAdmin()) {
            $config = json_encode(
                $this->model->jsConfig(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs = "<meta name='keymaster_config' content='$config'>";
            $l10n = json_encode(
                $this->model->jsL10n(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs .= "<meta name='keymaster_lang' content='$l10n'>";
            $filename = "{$this->pluginFolder}keymaster.min.js";
            $hjs .= $this->js($filename);
            $bjs = $this->view->render("dialog", []);
            return new Response("", $hjs, $bjs);
        }
        return new Response("");
    }

    private function js(string $filename): string
    {
        return <<<EOT
<script type="module" src="$filename"></script>
EOT;
    }
}
