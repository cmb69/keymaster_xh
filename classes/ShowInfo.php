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

use Keymaster\Infra\Response;
use Keymaster\Infra\SystemChecker;
use Keymaster\Infra\View;

class ShowInfo
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string> */
    private $lang;

    /** @var SystemChecker */
    private $systemChecker;

    /** @var View */
    private $view;

    /**
     * @param array<string> $lang
     */
    public function __construct(string $pluginFolder, array $lang, SystemChecker $systemChecker, View $view)
    {
        $this->pluginFolder = $pluginFolder;
        $this->lang = $lang;
        $this->view = $view;
        $this->systemChecker = $systemChecker;
    }

    public function __invoke(): Response
    {
        $output = $this->view->render("info", [
            "version" => KEYMASTER_VERSION,
            "checks" => $this->systemChecks(),
        ]);
        return Response::create($output);
    }

    /**
     * @return array<string,string>
     */
    private function systemChecks(): array
    {
        $phpVersion = '7.1.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[sprintf($this->lang['syscheck_phpversion'], $phpVersion)]
            = $this->systemChecker->checkVersion(PHP_VERSION, $phpVersion) ? 'xh_success' : 'xh_fail';
        foreach (array('json') as $ext) {
            $checks[sprintf($this->lang['syscheck_extension'], $ext)]
                = $this->systemChecker->checkExtension($ext) ? 'xh_success' : 'xh_fail';
        }
        $checks[sprintf($this->lang['syscheck_xhversion'], $xhVersion)]
            = $this->systemChecker->checkVersion(substr(CMSIMPLE_XH_VERSION, strlen("CMSimple_XH ")), $xhVersion)
                ? 'xh_success'
                : 'xh_fail';
        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = "{$this->pluginFolder}$folder";
        }
        foreach ($folders as $folder) {
            $checks[sprintf($this->lang['syscheck_writable_folder'], $folder)]
                = $this->systemChecker->checkWritability($folder) ? 'xh_success' : 'xh_warning';
        }
        $file = "{$this->pluginFolder}/key";
        $checks[sprintf($this->lang['syscheck_writable_file'], $file)]
            = $this->systemChecker->checkWritability($file) ? 'xh_success' : 'xh_fail';
        return $checks;
    }
}
