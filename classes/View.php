<?php

/**
 * The views class.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2019 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

namespace Keymaster;

/**
 * The views class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class View
{
    public function message(string $type, string $message): string
    {
        return XH_message($type, $message);
    }

    private function systemCheckItem(string $check, string $state): string
    {
        return "<p class=\"$state\">$check</p>";
    }

    /**
     * @param array<string,string> $checks An array of system checks.
     */
    private function systemCheck(array $checks): string
    {
        global $plugin_tx;

        $ptx = $plugin_tx['keymaster'];
        $items = '';
        foreach ($checks as $check => $state) {
            $items .= $this->systemCheckItem($check, $state);
        }
        return <<<EOT
<h4>$ptx[syscheck_title]</h4>
<ul class="keymaster_syscheck">
    $items
</ul>
EOT;
    }

    /**
     * @param array<string,string> $checks An array of system checks.
     */
    public function info(array $checks): string
    {
        return '<h1>Keymaster_XH ' . KEYMASTER_VERSION . '</h1>'
            . $this->systemCheck($checks);
    }
}
