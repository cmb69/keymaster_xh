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

use Keymaster\Model\Keymaster;
use Plib\Codec;
use Plib\DocumentStore;
use Plib\Random;
use Plib\Request;
use Plib\Response;
use Plib\View;

class Controller
{
    /** @var array<string,string> */
    private $conf;

    /** @var DocumentStore */
    private $store;

    /** @var Random */
    private $random;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(array $conf, DocumentStore $store, Random $random, View $view)
    {
        $this->conf = $conf;
        $this->store = $store;
        $this->random = $random;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->admin() && $request->cookie("keymaster_key") === null) {
            return Response::create();
        }
        $keymaster = Keymaster::updateIn($this->store);
        if (!$request->admin() && $request->cookie("keymaster_key") !== null) {
            $keymaster->revokeKey($request->cookie("keymaster_key"));
            $this->store->commit();
            return Response::create();
        }
        assert($request->admin());
        if (empty($request->cookie("keymaster_key")) && ($key = $keymaster->grantKey([$this, "generateKey"], $request->time(), (int) $this->conf["logout"])) !== null) {
            $this->store->commit();
            return Response::create()->withCookie("keymaster_key", $key, 0);
        }
        if ($keymaster->acceptKey($request->cookie("keymaster_key") ?? "", $request->time(), (int) $this->conf["logout"])) {
            $this->store->commit();
            return Response::create();
        }
        $this->store->rollback();
        return Response::error(409, $this->view->render("dialog", [
            "action" => $request->url()->with("logout")->relative(),
        ]));
    }

    public function generateKey(): string
    {
        return Codec::encodeBase32hex($this->random->bytes(15));
    }
}
