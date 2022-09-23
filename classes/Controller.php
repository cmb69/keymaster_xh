<?php

/**
 * Copyright (C) 2013-2019 Christoph M. Becker
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

class Controller
{
    /** @var Model */
    private $model;

    /** @var View */
    private $view;

    public function __construct()
    {
        global $pth, $plugin_cf, $plugin_tx;

        $filename = $pth['folder']['plugins'] . 'keymaster/key';
        $keyfile = new Keyfile($filename);
        $duration = $plugin_cf['keymaster']['logout'];
        $this->model = new Model($keyfile, $duration);
        $this->view = new View("{$pth["folder"]["plugins"]}keymaster/templates/", $plugin_tx['keymaster']);
    }

    public function run(): void
    {
        $this->emitScripts();
        $this->dispatch();
    }

    private function login(): void
    {
        global $o;

        if ($this->model->isFree()) {
            if ($this->model->give()) {
                // important, as filemtime() is called later for the same file:
                clearstatcache();
            } else {
                $o .= $this->view->error("error_write", $this->model->filename());
            }
        } else {
            $this->logout();
            exit;
        }
    }

    private function logout(): void
    {
        global $su;

        setcookie('status', '', 0, CMSIMPLE_ROOT);
        unset($_SESSION['xh_password']);
        header('Location: ' . CMSIMPLE_URL . '?' . $su, true, 303);
        exit;
    }

    /**
     * @return array<string,string>
     */
    private function systemChecks(): array
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['keymaster'];
        $phpVersion = '7.1.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'xh_success' : 'xh_fail';
        foreach (array('json') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'xh_success' : 'xh_fail';
        }
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(substr(CMSIMPLE_XH_VERSION, strlen("CMSimple_XH ")), $xhVersion) >= 0
                ? 'xh_success'
                : 'xh_fail';
        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'keymaster/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable_folder'], $folder)]
                = is_writable($folder) ? 'xh_success' : 'xh_warning';
        }
        $file = $pth['folder']['plugins'] . 'keymaster/key';
        $checks[sprintf($ptx['syscheck_writable_file'], $file)]
            = is_writable($file) ? 'xh_success' : 'xh_fail';
        return $checks;
    }

    private function wantsLogin(): bool
    {
        global $f;

        return isset($f) && $f == 'login';
    }

    private function isLogin(): bool
    {
        global $login;

        return isset($login) && $login == 'true'
            && (gc('status') != 'adm' || !logincheck());
    }

    private function isLogout(): bool
    {
        global $f;

        return isset($f) && $f == 'xh_loggedout';
    }

    private function emitScripts(): void
    {
        global $pth, $hjs, $bjs, $adm;

        if ($adm) {
            $config = json_encode(
                $this->model->jsConfig(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs .= "<meta name='keymaster_config' content='$config'>";
            $l10n = json_encode(
                $this->model->jsL10n(),
                JSON_HEX_APOS | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
            $hjs .= "<meta name='keymaster_lang' content='$l10n'>";
            $filename = $pth['folder']['plugins'] . 'keymaster/keymaster.min.js';
            $hjs .= $this->js($filename);
            $bjs .= $this->view->render("dialog", []);
        }
    }


    private function js(string $filename): string
    {
        return <<<EOT
<script type="module" src="$filename"></script>
EOT;
    }

    private function administration(): void
    {
        global $o, $admin;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= $this->view->render("info", [
                    "version" => KEYMASTER_VERSION,
                    "checks" => $this->systemChecks(),
                ]);
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    private function answerRemainingTime(): void
    {
        global $adm;

        if ($adm) {
            $remainingTime = $this->model->remainingTime();
        } else {
            $remainingTime = -1;
        }
        header('Content-Type: text/plain');
        echo $remainingTime;
        exit;
    }

    private function denyLogin(): void
    {
        global $o, $f;

        $o .= $this->view->error('editing');
        $f = '';
    }

    private function dispatch(): void
    {
        global $adm, $o;

        if ($this->wantsLogin() && !$this->model->isFree()) {
            $this->denyLogin();
        } elseif ($this->isLogin()) {
            $this->login();
        } elseif ($this->isLogout()) {
            if (!$this->model->take()) {
                $o .= $this->view->error("error_write", $this->model->filename());
            }
        } elseif (isset($_GET['keymaster_time'])) {
            $this->answerRemainingTime();
        } elseif ($adm && isset($_POST['keymaster_reset'])) {
            $this->model->reset();
            exit;
        } elseif ($adm && !$this->model->sessionHasExpired()) {
            if (!$this->model->reset()) {
                $o .= $this->view->error("error_write", $this->model->filename());
            }
        } elseif ($adm) {
            $this->logout();
        }

        if ($adm) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('keymaster')) {
                $this->administration();
            }
        }
    }
}
