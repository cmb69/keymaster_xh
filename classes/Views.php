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

/**
 * The views class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Keymaster_Views
{
    /**
     * The model.
     *
     * @var array
     *
     * @access private
     */
    var $_model;

    /**
     * Initializes a new instance.
     *
     * @param Keymaster_Model $model A model.
     *
     * @return void
     */
    function Keymaster_Views(Keymaster_Model $model)
    {
        $this->_model = $model;
    }

    /**
     * Returns a string with TAGCs adjusted for (X)HTML.
     *
     * @param string $string A string.
     *
     * @return string (X)HTML.
     *
     * @access protected
     *
     * @global array The configuration of the core.
     */
    function xhtml($string)
    {
        global $cf;

        if ($cf['xhtml']['endtags'] != 'true') {
            $string = str_replace(' />', '>', $string);
        }
        return $string;
    }

    /**
     * Returns a JSON encoded value.
     *
     * @param mixed $value A value.
     *
     * @return string
     *
     * @access protected
     *
     * @global array The paths of system files and folders.
     */
    function json($value)
    {
        global $pth;

        if (function_exists('json_encode')) {
            return json_encode($value);
        } else {
            return $this->encodeJson($value);
        }
    }

    /**
     * Returns a PHP value encoded as JSON string.
     *
     * A fallback for json_encode().
     *
     * @param mixed $value A PHP value.
     *
     * @return string
     *
     * @access public
     */
    function encodeJson($value)
    {
        switch (gettype($value)) {
        case 'boolean':
            return $value ? 'true' : 'false';
        case 'integer':
        case 'double':
            return $value;
        case 'string':
            return '"' . $this->quoteJsonString($value) . '"';
        case 'array':
            if (array_keys($value) === range(0, count($value) - 1)) {
                // encode as array
                $elts = array();
                foreach ($value as $val) {
                    $elts[] = $this->encodeJson($val);
                }
                return '[' . implode(',', $elts) . ']';
            } else {
                // encode as object
                $members = array();
                foreach ($value as $key => $val) {
                    $members[] = '"' . $this->quoteJsonString($key) . '":'
                        . $this->encodeJson($val);
                }
                return '{' . implode(',', $members) . '}';
            }
        case 'object':
            return $this->encodeJson(get_object_vars($value));
        case 'NULL':
            return 'null';
        default:
            $msg = __FUNCTION__ . '(): type is unsupported, encoded as null';
            trigger_error($msg, E_USER_WARNING);
            return 'null';
        }
    }

    /**
     * Quotes a string for use as JSON string.
     *
     * @param string $string A string.
     *
     * @return string
     *
     * @access protected
     */
    function quoteJsonString($string)
    {
        $string = addcslashes($string, "\"\\/");
        $escape = create_function(
            '$matches', 'return sprintf("\\u%04X", ord($matches[0]));'
        );
        $string = preg_replace_callback('/[\x00-\x1f]/', $escape, $string);
        return $string;
    }

    /**
     * Returns a message.
     *
     * @param string $type    A message type ('success', 'info', 'warning', 'fail').
     * @param string $message A message.
     *
     * @return string (X)HTML.
     */
    function message($type, $message)
    {
        if (function_exists('XH_message')) {
            return XH_message($type, $message);
        } else {
            $class = in_array($type, array('warning', 'fail'))
                ? 'cmsimplecore_warning'
                : '';
            return '<p class="' . $class . '">' . $message . '</p>';
        }
    }

    /**
     * Returns a system check item view.
     *
     * @param string $check A system check label.
     * @param string $state A system check state.
     *
     * @return string XHTML.
     *
     * @access protected
     */
    function systemCheckItem($check, $state)
    {
        $icon = $this->_model->stateIconPath($state);
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
     * @access protected
     *
     * @global array The localization of the plugins.
     */
    function systemCheck($checks)
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
     *
     * @access protected
     */
    function about()
    {
        global $plugin_tx;

        $ptx = $plugin_tx['keymaster'];
        $version = KEYMASTER_VERSION;
        $icon = $this->_model->pluginIconPath();
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
     *
     * @access public
     */
    function info($checks)
    {
        $o = '<h1>Keymaster_XH</h1>'
            . $this->systemCheck($checks)
            . $this->about();
        return $this->xhtml($o);
    }

    /**
     * Returns the script elements.
     *
     * @param string $filename A JS script filename.
     *
     * @return string (X)HTML.
     *
     * @access public
     */
    function js($filename)
    {
        $config = $this->json($this->_model->jsConfig());
        $l10n = $this->json($this->_model->jsL10n());
        return <<<EOT
<script type="text/javascript" src="$filename"></script>
<script type="text/javascript">/* <![CDATA[ */
    Keymaster.config = $config;
    Keymaster.l10n = $l10n;
/* ]]> */</script>
EOT;
    }
}

?>
