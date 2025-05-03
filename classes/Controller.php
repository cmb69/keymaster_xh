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

use Keymaster\Model\Model;
use Plib\Codec;
use Plib\Random;
use Plib\Request;
use Plib\Response;
use Plib\View;

class Controller
{
    /** @var Model */
    private $model;

    /** @var Random */
    private $random;

    /** @var View */
    private $view;

    public function __construct(Model $model, Random $random, View $view)
    {
        $this->model = $model;
        $this->random = $random;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->admin() && $request->cookie("keymaster_key") === null) {
            return Response::create();
        }
        if (!$request->admin() && $request->cookie("keymaster_key") !== null) {
            $this->model->revokeKey($request->cookie("keymaster_key"));
            return Response::create();
        }
        assert($request->admin());
        if (empty($request->cookie("keymaster_key")) && ($key = $this->model->claimKey([$this, "generateKey"])) !== null) {
            return Response::create()->withCookie("keymaster_key", $key, 0);
        }
        if ($this->model->checkKey($request->cookie("keymaster_key") ?? "")) {
            return Response::create();
        }
        return Response::error(409, $this->view->render("dialog", [
            "action" => $request->url()->with("logout")->relative(),
        ]));
    }

    public function generateKey(): string
    {
        return Codec::encodeBase32hex($this->random->bytes(15));
    }
}
