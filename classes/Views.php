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
        return "<p class=\"$state\">$check</p>";
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
     * Returns the plugin information view.
     *
     * @param array $checks An array of system checks.
     *
     * @return string (X)HTML.
     */
    public function info($checks)
    {
        return '<h1>Keymaster_XH ' . KEYMASTER_VERSION . '</h1>'
            . $this->systemCheck($checks);
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
