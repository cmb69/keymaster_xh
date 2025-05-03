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

use Plib\Request;
use Plib\Response;
use Plib\SystemChecker;
use Plib\View;

class ShowInfo
{
    /** @var string */
    private $pluginFolder;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    public function __construct(string $pluginFolder, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->view = $view;
        $this->systemChecker = $systemChecker;
    }

    public function __invoke(Request $request): Response
    {
        return Response::create($this->view->render("info", [
            "version" => KEYMASTER_VERSION,
            "checks" => $this->checks(),
        ]));
    }

    /** @return list<array{key:string,arg:string,class:string}> */
    private function checks(): array
    {
        return [
            $this->checkPhpVersion("7.1.0"),
            $this->checkPhpExtension("json"),
            $this->checkXhVersion("1.7.0"),
            $this->checkPlibVersion("1.7"),
            $this->checkFolderWritability($this->pluginFolder . "config/"),
            $this->checkFolderWritability($this->pluginFolder . "css/"),
            $this->checkFolderWritability($this->pluginFolder . "languages/"),
            $this->checkFileWritability($this->pluginFolder . "key"),
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkPhpVersion(string $version): array
    {
        $check = $this->systemChecker->checkVersion(PHP_VERSION, $version);
        return [
            "key" => "syscheck_phpversion",
            "arg" => $version,
            "class" => $check ? "xh_success" : "xh_fail",
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkPhpExtension(string $name): array
    {
        $check = $this->systemChecker->checkExtension($name);
        return [
            "key" => "syscheck_extension",
            "arg" => $name,
            "class" => $check ? "xh_success" : "xh_fail",
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkXhVersion(string $version): array
    {
        $check = $this->systemChecker->checkVersion(CMSIMPLE_XH_VERSION, "CMSimple_XH $version");
        return [
            "key" => "syscheck_xhversion",
            "arg" => $version,
            "class" => $check ? "xh_success" : "xh_fail",
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkPlibVersion(string $version): array
    {
        $check = $this->systemChecker->checkPlugin("plib", $version);
        return [
            "key" => "syscheck_plibversion",
            "arg" => $version,
            "class" => $check ? "xh_success" : "xh_fail",
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkFolderWritability(string $foldername): array
    {
        $check = $this->systemChecker->checkWritability($foldername);
        return [
            "key" => "syscheck_writable_folder",
            "arg" => $foldername,
            "class" => $check ? "xh_success" : "xh_warning",
        ];
    }

    /** @return array{key:string,arg:string,class:string} */
    private function checkFileWritability(string $filename): array
    {
        $check = $this->systemChecker->checkWritability($filename);
        return [
            "key" => "syscheck_writable_file",
            "arg" => $filename,
            "class" => $check ? "xh_success" : "xh_fail",
        ];
    }
}
