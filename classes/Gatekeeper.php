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

class Gatekeeper
{
    /** @var string */
    private $pluginFolder;

    /** @var array<string,string> */
    private $conf;

    /** @var DocumentStore */
    private $store;

    /** @var Random */
    private $random;

    /** @var View */
    private $view;

    /** @param array<string,string> $conf */
    public function __construct(
        string $pluginFolder,
        array $conf,
        DocumentStore $store,
        Random $random,
        View $view
    ) {
        $this->pluginFolder = $pluginFolder;
        $this->conf = $conf;
        $this->store = $store;
        $this->random = $random;
        $this->view = $view;
    }

    public function __invoke(Request $request): Response
    {
        if (!$request->admin() && empty($request->cookie("keymaster_key"))) {
            return Response::create();
        }
        $keymaster = Keymaster::updateIn($this->store);
        if (!$request->admin() && !empty($request->cookie("keymaster_key"))) {
            return $this->revokeKey($request, $keymaster);
        }
        assert($request->admin());
        if (($response = $this->acceptKey($request, $keymaster)) !== null) {
            return $response;
        }
        if (($response = $this->grantKey($request, $keymaster)) !== null) {
            return $response;
        }
        $this->store->rollback();
        return Response::error(409, $this->renderLockScreen($request));
    }

    private function revokeKey(Request $request, Keymaster $keymaster): Response
    {
        $keymaster->revokeKey($request->cookie("keymaster_key") ?? "");
        if (!$this->store->commit()) {
            return Response::create($this->view->message("fail", "error_save"));
        }
        return Response::create()->withCookie("keymaster_key", "", 1);
    }

    private function grantKey(Request $request, Keymaster $keymaster): ?Response
    {
        $key = $keymaster->grantKey([$this, "generateKey"], $request->time(), 60 * (int) $this->conf["period_lock"]);
        if ($key === null) {
            return null;
        }
        if (!$this->store->commit()) {
            return Response::create($this->view->message("fail", "error_save"));
        }
        return Response::create()->withCookie("keymaster_key", $key, 0);
    }

    private function acceptKey(Request $request, Keymaster $keymaster): ?Response
    {
        $duration = 60 * (int) $this->conf["period_lock"];
        if (!$keymaster->acceptKey($request->cookie("keymaster_key") ?? "", $request->time(), $duration)) {
            return null;
        }
        if (!$this->store->commit()) {
            return Response::create($this->view->message("fail", "error_save"));
        }
        return Response::create();
    }

    private function renderLockScreen(Request $request): string
    {
        return $this->view->render("lock_screen", [
            "action" => $request->url()->with("logout")->relative(),
            "stylesheet" => $request->url()->path($this->pluginFolder . "css/stylesheet.css")->relative(),
            "retry_min" => (int) $this->conf["period_retry"],
            "retry_sec" => 60 * (int) $this->conf["period_retry"],
        ]);
    }

    public function generateKey(): string
    {
        return Codec::encodeBase32hex($this->random->bytes(15));
    }
}
