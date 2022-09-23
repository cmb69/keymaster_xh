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
    public function isAdmin(): bool
    {
        global $adm;

        return $adm;
    }

    public function wantsLogin(): bool
    {
        global $f;

        return isset($f) && $f === "login";
    }

    public function isLogout(): bool
    {
        global $f;

        return isset($f) && $f === "xh_loggedout";
    }
}
