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

use Keymaster\Dic;
use Plib\Request;

if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * @var string $admin
 * @var string $o
 */

XH_registerStandardPluginMenuItems(false);

if (XH_wantsPluginAdministration("keymaster")) {
    $o .= print_plugin_admin("off");
    switch ($admin) {
        case "":
            $o .= Dic::makeShowInfo()(Request::current())();
            break;
        default:
            $o .= plugin_admin_common();
    }
}
