<?php

/**
 * Front-end of Keymaster_XH.
 *
 * PHP versions 4 and 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}

/**
 * The version number of the plugin.
 */
define('KEYMASTER_VERSION', '@KEYMASTER_VERSION@');

/**
 * The base URL.
 *
 * @todo Fix.
 */
define(
    'KEYMASTER_URL',
    'http'
    . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
    . '://' . $_SERVER['SERVER_NAME']
    . ($_SERVER['SERVER_PORT'] < 1024 ? '' : ':' . $_SERVER['SERVER_PORT'])
    . preg_replace('/index.php$/', '', $_SERVER['SCRIPT_NAME'])
);

/**
 * The model class.
 */
require_once $pth['folder']['plugin_classes'] . 'model.php';

/**
 * The model object.
 *
 * @var Keymaster
 */
$_Keymaster = new Keymaster(
    $pth['folder']['plugins'] . 'keymaster/key',
    $plugin_cf['keymaster']['logout']
);

/*
 * Handle login screen, logout
 * and secondary browser windows after the user has logged out.
 */
if (isset($f) && $f == 'login' && !$_Keymaster->isFree()) {
    $o .= '<div class="cmsimplecore_warning">'
        . $plugin_tx['keymaster']['editing'] . '</div>';
    $f = '';
} elseif ($logout && $_COOKIE['status'] == 'adm' && logincheck()) {
    if (!$_Keymaster->take()) {
        e('cntwriteto', 'file', $_Keymaster->getFilename());
    }
} elseif (!$adm && isset($_GET['keymaster_time'])) {
    header('Content-Type: text/plain');
    echo -1;
    exit;
}

?>
