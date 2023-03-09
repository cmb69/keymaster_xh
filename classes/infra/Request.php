<?php

/**
 * Copyright (C) 2022 Christoph M. Becker
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

namespace Keymaster\Infra;

class Request
{
    /** @codeCoverageIgnore */
    public static function current(): self
    {
        return new Request;
    }

    /** @codeCoverageIgnore */
    public function adm(): bool
    {
        global $adm;
        return $adm;
    }

    public function wantsLogin(): bool
    {
        return $this->f() === "login";
    }

    public function isLogin(): bool
    {
        return $this->login() === "true"
            && (($this->cookie()["status"] ?? null) !== "adm" || !$this->logincheck());
    }

    /** @codeCoverageIgnore */
    protected function login(): ?string
    {
        global $login;
        return $login;
    }

    /**
     * @return array<string,string>
     * @codeCoverageIgnore
     */
    protected function cookie(): array
    {
        return $_COOKIE;
    }

    /** @codeCoverageIgnore */
    protected function logincheck(): bool
    {
        return logincheck();
    }

    public function isLogout(): bool
    {
        return $this->f() === "xh_loggedout";
    }

    /** @codeCoverageIgnore */
    protected function f(): string
    {
        global $f;
        return $f;
    }

    public function isTimeRequested(): bool
    {
        $get = $this->get();
        return isset($get["keymaster_time"]);
    }

    /**
     * @return array<string,string|array<string>>
     * @codeCoverageIgnore
     */
    protected function get(): array
    {
        return $_GET;
    }

    public function isResetRequested(): bool
    {
        $post = $this->post();
        return isset($post["keymaster_reset"]);
    }

    /**
     * @return array<string,string|array<string>>
     * @codeCoverageIgnore
     */
    protected function post(): array
    {
        return $_POST;
    }

    /** @codeCoverageIgnore */
    public function su(): string
    {
        global $su;
        return $su;
    }
}
