<?php

/**
 * Back-end of Keymaster_XH.
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
 * Fallback for missing json_encode().
 */
if (!function_exists('json_encode')) {
    if (!class_exists('CMB_JSON')) {
        include_once $pth['folder']['plugin'] . 'JSON.php';
    }
    /**
     * Converts a value to a JSON string.
     *
     * @param mixed $value A value.
     *
     * @return string
     *
     * @todo Don't define json_encode().
     */
    function json_encode($value)
    {
        $json = CMB_JSON::instance();
        return $json->encode($value);
    }
}

/**
 * Logs out the admin.
 *
 * @return void
 *
 * @global string The current page URL.
 *
 * @todo Decouple that from knowledge about login credentials,
 *       i.e. use redirect to ?logout.
 */
function Keymaster_logout()
{
    global $su;

    setcookie('status', '', 0, CMSIMPLE_ROOT);
    setcookie('passwd', '', 0, CMSIMPLE_ROOT);
    header('Location: ' . KEYMASTER_URL . '?' . $su, true, 303);
}

/**
 * Returns an array with system checks.
 *
 * @return array
 *
 * @global array  The paths of system files and folders.
 * @global array  The localization of the core.
 * @global array  The localization of the plugins.
 */
function Keymaster_systemChecks()
{
    global $pth, $tx, $plugin_tx;

    $ptx = $plugin_tx['keymaster'];
    $phpVersion = '4.3.0';
    $checks = array();
    $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
        = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
    foreach (array('pcre') as $ext) {
        $checks[sprintf($ptx['syscheck_extension'], $ext)]
            = extension_loaded($ext) ? 'ok' : 'fail';
    }
    $checks[$ptx['syscheck_magic_quotes']]
        = !get_magic_quotes_runtime() ? 'ok' : 'fail';
    $checks[$ptx['syscheck_encoding']]
        = strtoupper($tx['meta']['codepage']) == 'UTF-8' ? 'ok' : 'warn';
    $folders = array();
    foreach (array('config/', 'css/', 'languages/') as $folder) {
        $folders[] = $pth['folder']['plugins'] . 'keymaster/' . $folder;
    }
    foreach ($folders as $folder) {
        $checks[sprintf($ptx['syscheck_writable_folder'], $folder)]
            = is_writable($folder) ? 'ok' : 'warn';
    }
    $file = $pth['folder']['plugins'] . 'keymaster/key';
    $checks[sprintf($ptx['syscheck_writable_file'], $file)]
        = is_writable($file) ? 'ok' : 'fail';
    return $checks;
}

/*
 * Handle login request.
 */
if (isset($login) && $login == 'true'
    && (gc('status') != 'adm' || !logincheck())
) {
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
    $bjs .= $_Keymaster_views->js($pth['folder']['plugins'] . 'keymaster/admin.js');
} else {
    $o .= $_Keymaster_views->js($pth['folder']['plugins'] . 'keymaster/admin.js');
}

/*
 * Handle the plugin adminstration.
 */
if (isset($keymaster) && $keymaster == 'true') {
    $o .= print_plugin_admin('off');
    switch ($admin) {
    case '':
        $o .= $_Keymaster_views->info(Keymaster_systemChecks());
        break;
    default:
        $o .= plugin_admin_common($action, $admin, $plugin);
    }
}

?>
