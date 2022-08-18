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
class Views
{
    /**
     * The model.
     *
     * @var array
     */
    private $model;

    /**
     * Initializes a new instance.
     *
     * @param Model $model A model.
     *
     * @return void
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Returns a message.
     *
     * @param string $type    A message type ('success', 'info', 'warning', 'fail').
     * @param string $message A message.
     *
     * @return string (X)HTML.
     */
    public function message($type, $message)
    {
        return XH_message($type, $message);
    }

    /**
     * Returns a system check item view.
     *
     * @param string $check A system check label.
     * @param string $state A system check state.
     *
     * @return string XHTML.
     */
    private function systemCheckItem($check, $state)
    {
        $icon = $this->model->stateIconPath($state);
        return <<<EOT
<li>
    <img src="$icon" alt="$state" class="keymaster_state_icon" />
    <span>$check</span>
</li>
EOT;
    }

    /**
     * Returns the system check view.
     *
     * @param array $checks An array of system checks.
     *
     * @return string XHTML.
     *
     * @global array The localization of the plugins.
     */
    private function systemCheck($checks)
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
     * Returns the about view.
     *
     * @return string XHTML.
     *
     * @global array The localization of the plugins.
     */
    private function about()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['keymaster'];
        $version = KEYMASTER_VERSION;
        $icon = $this->model->pluginIconPath();
        $alt = $ptx['alt_logo'];
        return <<<EOT
<h4>$ptx[about]</h4>
<img src="$icon" class="keymaster_plugin_icon" alt="$alt" />
<p>Version: $version</p>
<p>Copyright &copy; 2013-2019 <a href="http://3-magi.net/">Christoph M. Becker</a>
</p>
<p class="keymaster_license">
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.</p>
<p class="keymaster_license">
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.</p>
<p class="keymaster_license">
    You should have received a copy of the GNU General Public License
    along with this program.  If not, see
    <a href="http://www.gnu.org/licenses/">http://www.gnu.org/licenses/</a>.</p>
EOT;
    }

    /**
     * Returns the plugin information view.
     *
     * @param array $checks An array of system checks.
     *
     * @return string (X)HTML.
     */
    public function info($checks)
    {
        return '<h1>Keymaster_XH</h1>'
            . $this->systemCheck($checks)
            . $this->about();
    }

    /**
     * Returns the script elements.
     *
     * @param string $filename A JS script filename.
     *
     * @return string (X)HTML.
     */
    public function js($filename)
    {
        $config = json_encode($this->model->jsConfig());
        $l10n = json_encode($this->model->jsL10n());
        return <<<EOT
<script type="text/javascript" src="$filename"></script>
<script type="text/javascript">/* <![CDATA[ */
    Keymaster.config = $config;
    Keymaster.l10n = $l10n;
/* ]]> */</script>
EOT;
    }
}
