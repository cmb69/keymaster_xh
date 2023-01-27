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

class Controller
{
    /** @var Request */
    private $request;

    /** @var Model */
    private $model;

    /** @var View */
    private $view;

    public function __construct(Request $request)
    {
        global $pth, $plugin_cf, $plugin_tx;

        $filename = $pth['folder']['plugins'] . 'keymaster/key';
        $keyfile = new Keyfile($filename);
        $duration = $plugin_cf['keymaster']['logout'];
        $this->request = $request;
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

    private function isLogin(): bool
    {
        global $login;

        return isset($login) && $login == 'true'
            && (gc('status') != 'adm' || !logincheck());
    }

    private function emitScripts(): void
    {
        global $pth;

        $controller = new EmitScripts(
            "{$pth['folder']['plugins']}/keymaster/",
            $this->request,
            $this->model, $this->view
        );
        $controller()->process();
    }


    private function administration(): void
    {
        global $pth, $plugin_tx, $o, $admin;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $controller = new ShowInfo("{$pth['folder']['plugins']}/keymaster/", $plugin_tx['keymaster'], $this->view);
                $o .= $controller()->process();
                break;
            default:
                $o .= plugin_admin_common();
        }
    }

    private function answerRemainingTime(): void
    {
        if ($this->request->isAdmin()) {
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
        global $o;

        if ($this->request->wantsLogin() && !$this->model->isFree()) {
            $this->denyLogin();
        } elseif ($this->isLogin()) {
            $this->login();
        } elseif ($this->request->isLogout()) {
            if (!$this->model->take()) {
                $o .= $this->view->error("error_write", $this->model->filename());
            }
        } elseif (isset($_GET['keymaster_time'])) {
            $this->answerRemainingTime();
        } elseif ($this->request->isAdmin() && isset($_POST['keymaster_reset'])) {
            $this->model->reset();
            exit;
        } elseif ($this->request->isAdmin() && !$this->model->sessionHasExpired()) {
            if (!$this->model->reset()) {
                $o .= $this->view->error("error_write", $this->model->filename());
            }
        } elseif ($this->request->isAdmin()) {
            $this->logout();
        }

        if ($this->request->isAdmin()) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('keymaster')) {
                $this->administration();
            }
        }
    }
}
