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

class Controller
{
    /** @var Model */
    private $model;

    /** @var View */
    private $view;

    public function __construct(Model $model, View $view)
    {
        $this->model = $model;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        if ($request->wantsLogin() && !$this->model->isFree()) {
            return $this->denyLogin();
        }
        if ($request->isLogin()) {
            return $this->login($request);
        }
        if ($request->isLogout()) {
            if (!$this->model->take()) {
                return Response::create($this->view->error("error_write", $this->model->filename()));
            }
            return Response::create();
        }
        if ($request->isTimeRequested()) {
            return $this->answerRemainingTime($request->isAdmin());
        }
        if ($request->isAdmin() && $request->isResetRequested()) {
            return Response::create((string) $this->model->reset())->withContentType("text/plain");
        }
        if ($request->isAdmin() && !$this->model->sessionHasExpired()) {
            if (!$this->model->reset()) {
                return Response::create($this->view->error("error_write", $this->model->filename()));
            }
            return Response::create();
        }
        if ($request->isAdmin()) {
            return $this->logout($request);
        }
        return Response::create();
    }

    private function login(Request $request): Response
    {
        if (!$this->model->isFree()) {
            return $this->logout($request);
        }
        if (!$this->model->give()) {
            return Response::create($this->view->error("error_write", $this->model->filename()));
        }
        return Response::create();
    }

    private function logout(Request $request): Response
    {
        return Response::redirect(CMSIMPLE_URL . '?' . $request->su())->withLogout();
    }

    private function answerRemainingTime(bool $isAdmin): Response
    {
        if ($isAdmin) {
            $remainingTime = $this->model->remainingTime();
        } else {
            $remainingTime = -1; // TODO: 403 Forbidden?
        }
        return Response::create((string) $remainingTime)->withContentType("text/plain");
    }

    private function denyLogin(): Response
    {
        return Response::create($this->view->error('editing'))->withClearedF();
    }
}
