<?php

/**
 * Copyright 2023 Christoph M. Becker
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

class Responder
{
    /**
     * @return string|never
     * @codeCoverageIgnore
     */
    public static function respond(Response $response)
    {
        return (new Responder)->doRespond($response);
    }

    /** @return string|never */
    public function doRespond(Response $response)
    {
        if ($response->logout()) {
            $this->setcookie("status", "", 0, CMSIMPLE_ROOT);
            $this->unsetSessionPassword();
        }
        if ($response->location() !== null) {
            $this->purgeOutputBuffers();
            $this->header("Location: " . $response->location(), true, 303);
            $this->exit();
        }
        if ($response->contentType() !== null) {
            $this->purgeOutputBuffers();
            $this->header("Content-Type: " . $response->contentType());
            $this->print($response->output());
            $this->exit();
        }
        if ($response->hjs() !== null) {
            $this->hjs($response->hjs());
        }
        if ($response->bjs() !== null) {
            $this->bjs($response->bjs());
        }
        if ($response->clearedF()) {
            $this->f("");
        }
        return $response->output();
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function unsetSessionPassword()
    {
        unset($_SESSION["xh_password"]);
    }

    /** @codeCoverageIgnore */
    protected function setcookie(string $name, string $value, int $expires, string $path): bool
    {
        return setcookie($name, $value, $expires, $path);
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function header(string $header, bool $replace = true, int $responseCode = 0)
    {
        header($header, $replace, $responseCode);
    }

    /**
     * @return never
     * @codeCoverageIgnore
     */
    protected function exit()
    {
        exit;
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function print(string $string)
    {
        print $string;
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function purgeOutputBuffers()
    {
        while (ob_get_level()) {
            ob_end_clean();
        }
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function hjs(string $string)
    {
        global $hjs;
        $hjs .= $string;
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function bjs(string $string)
    {
        global $bjs;
        $bjs .= $string;
    }

    /**
     * @return void
     * @codeCoverageIgnore
     */
    protected function f(string $string)
    {
        global $f;
        $f = $string;
    }
}
