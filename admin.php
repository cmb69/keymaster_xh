<?php

/**
 * Back-end of Keymaster_XH.
 *
 * @package	Keymaster
 * @copyright	Copyright (c) 2013 Christoph M. Becker <http://3-magi.net/>
 * @license	http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version     $Id$
 * @link	http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */


/*
 * Prevent direct access.
 */
if (!defined('CMSIMPLE_XH_VERSION')) {
    header('HTTP/1.0 403 Forbidden');
    exit;
}


/**
 * Fallback for missing json_encode().
 */
if (!function_exists('json_encode')) {
    if (!class_exists('CMB_JSON')) {
	include_once $pth['folder']['plugin'] . 'JSON.php';
    }
    function json_encode($value)
    {
	$json = CMB_JSON::instance();
	return $json->encode($value);
    }
}


function Keymaster_logout()
{
    global $su;

    setcookie('status', '', 0, CMSIMPLE_ROOT);
    setcookie('passwd', '', 0, CMSIMPLE_ROOT);
    header('Location: ' . KEYMASTER_URL . '?' . $su, true, 303);
}


/**
 * Returns a text with special characters converted to HTML entities.
 *
 * @param  string $text
 * @return string  The (X)HTML.
 */
function Keymaster_hsc($text)
{
    return htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
}


/**
 * Renders a template.
 *
 * @global array  The paths of system files and folders.
 * @global array  The configuration of the core.
 * @param  string  $_template  The name of the template.
 * @param  string  $_bag  Variables available in the template.
 * @return string  The (X)HTML.
 */
function Keymaster_render($_template, $_bag)
{
    global $pth, $cf;

    $_template = "{$pth['folder']['plugins']}keymaster/views/$_template.htm";
    $_xhtml = $cf['xhtml']['endtags'];
    unset($pth, $cf);
    extract($_bag);
    ob_start();
    include $_template;
    $o = ob_get_clean();
    if (!$_xhtml) {
	$o = str_replace('/>', '>', $o);
    }
    return $o;
}


/**
 * Returns the plugin information view.
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the core.
 * @global array  The localization of the plugins.
 * @global Boilerplate_Model  The model.
 * @return string  The (X)HTML.
 */
function Keymaster_info() // RELEASE-TODO
{
    global $pth, $tx, $plugin_tx;

    $ptx = $plugin_tx['keymaster'];
    $labels = array(
	'syscheck' => $ptx['syscheck_title'],
	'about' => $ptx['about']
    );
    $labels = array_map('Keymaster_hsc', $labels);
    $phpVersion = '4.3.0';
    foreach (array('ok', 'warn', 'fail') as $state) {
        $images[$state] = $pth['folder']['plugins']
	    . "keymaster/images/$state.png";
    }
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)] =
        version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('date', 'pcre') as $ext) {
	$checks[sprintf($ptx['syscheck_extension'], $ext)] =
	    extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']] =
        !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']] =
        strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    $folders = array();
    foreach (array('config/', 'css', 'languages/') as $folder) {
	$folders[] = $pth['folder']['plugins'] . 'keymaster/' . $folder;
    }
    foreach ($folders as $folder) {
	$checks[sprintf($ptx['syscheck_writable_folder'], $folder)] =
            is_writable($folder) ? 'ok' : 'warn';
    }
    $file = $pth['folder']['plugins'] . 'keymaster/key';
    $checks[sprintf($ptx['syscheck_writable_file'], $file)] =
        is_writable($file) ? 'ok' : 'fail';
    $icon = $pth['folder']['plugins'] . 'keymaster/keymaster.png';
    $version = KEYMASTER_VERSION;
    $bag = compact('labels', 'images', 'checks', 'icon', 'version');
    return Keymaster_render('info', $bag);
}


/**
 * Returns the script elements.
 *
 * @return void
 */
function Keymaster_JS()
{
    global $o, $pth, $plugin_cf, $plugin_tx;

    $pcf = $plugin_cf['keymaster'];
    $ptx = $plugin_tx['keymaster'];
    $data = array(
        'warn' => $pcf['logout'] - $pcf['warn'],
        'pollInterval' => $pcf['poll'],
        'text' => $ptx
    );
    return '<script type="text/javascript">/* <![CDATA[ */keymaster = '
        . json_encode($data) . '/* ]]> */</script>'
        . '<script type="text/javascript" src="'
        . $pth['folder']['plugins'] . 'keymaster/admin.min.js"></script>';
}


/*
 * Handle login request.
 */
if (isset($login) && $login == 'true' && (gc('status') != 'adm' || !logincheck()))
{
    if ($_Keymaster->isFree()) {
        if (!$_Keymaster->give()) {
            e('cntwriteto', 'file', $_Keymaster->getFilename());
        } else {
	    // important, as filemtime() is called later for the same file:
	    clearstatcache();
	}
    } else {
        Keymaster_logout();
        exit;
    }
}


/*
 * Handle Keymaster XHRs and other requests.
 */
if (isset($_GET['keymaster_time'])) {
    header('Content-Type: text/plain');
    echo $_Keymaster->remainingTime();
    exit;
} elseif (isset($_POST['keymaster_reset'])) {
    $_Keymaster->reset();
    exit;
} elseif ($_Keymaster->remainingTime() > 0) {
    if (!$_Keymaster->reset()) {
        e('cntwriteto', 'file', $_Keymaster->getFilename());
    }
} else {
    Keymaster_logout();
    exit;
}


/*
 * Emit the script elements.
 */
if (isset($bjs)) {
    $bjs .= Keymaster_JS();
} else {
    $o .= Keymaster_JS();
}


/*
 * Handle the plugin adminstration.
 */
if (isset($keymaster) && $keymaster == 'true') {
    $o .= print_plugin_admin('off');
    switch ($admin) {
    case '':
        $o .= Keymaster_info();
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
