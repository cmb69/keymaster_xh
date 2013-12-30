<?php

/**
 * The controller class.
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

/**
 * The controller class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Keymaster_Controller
{
    /**
     * The model.
     *
     * @var Keymaster_Model
     *
     * @access private
     */
    var $_model;

    /**
     * The views.
     *
     * @var Keymaster_Views
     *
     * @access private
     */
    var $_views;

    /**
     * Initializes a new instance.
     *
     * @access public
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    function Keymaster_Controller()
    {
        global $pth, $plugin_cf;

        $filename = $pth['folder']['plugins'] . 'keymaster/key';
        $keyfile = new Keymaster_Keyfile($filename);
        $duration = $plugin_cf['keymaster']['logout'];
        $this->_model = new Keymaster_Model($keyfile, $duration);
        $this->_views = new Keymaster_Views($this->_model);
        $this->emitScripts();
        $this->dispatch();
    }

    /**
     * Returns the CMSimple_XH version.
     *
     * Unfortunately, we can't use CMSIMPLE_XH_VERSION directly, as this is set
     * by CMSimple v4.
     *
     * @return string
     *
     * @access protected
     */
    function xhVersion()
    {
        $version = CMSIMPLE_XH_VERSION;
        if (strpos($version, 'CMSimple_XH') === 0) {
            $version = substr($version, strlen('CMSimple_XH '));
        } else {
            $version = '0';
        }
        return $version;
    }
    /**
     * The absolute URL of the CMSimple_XH installation.
     *
     * @return string
     *
     * @global string The script name.
     */
    function baseUrl()
    {
        global $sn;

        return 'http'
            . (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off' ? 's' : '')
            . '://' . $_SERVER['HTTP_HOST'] . preg_replace('/index.php$/', '', $sn);
    }

    /**
     * Handles a login.
     *
     * @return void
     *
     * @access protected
     */
    function login()
    {
        if ($this->_model->isFree()) {
            if ($this->_model->give()) {
                // important, as filemtime() is called later for the same file:
                clearstatcache();
            } else {
                e('cntwriteto', 'file', $this->_model->filename());
            }
        } else {
            $this->logout();
            exit;
        }
    }

    /**
     * Logs the admin out.
     *
     * @return void
     *
     * @access protected
     *
     * @global string The current page URL.
     */
    function logout()
    {
        global $su;

        setcookie('status', '', 0, CMSIMPLE_ROOT);
        if (version_compare($this->xhVersion(), '1.6dev', 'ge')) {
            unset($_SESSION['xh_password'][CMSIMPLE_ROOT]);
        } else {
            setcookie('passwd', '', 0, CMSIMPLE_ROOT);
        }
        header('Location: ' . $this->baseUrl() . '?' . $su, true, 303);
        exit;
    }

    /**
     * Returns an array with system checks.
     *
     * @return array
     *
     * @access protected
     *
     * @global array  The paths of system files and folders.
     * @global array  The localization of the core.
     * @global array  The localization of the plugins.
     */
    function systemChecks()
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

    /**
     * Returns whether the admin wants to log in.
     *
     * @return bool
     *
     * @access protected
     *
     * @global string The current system function.
     */
    function wantsLogin()
    {
        global $f;

        return isset($f) && $f == 'login';
    }

    /**
     * Returns whether the admin has just logged in.
     *
     * @return bool
     *
     * @access protected
     *
     */
    function isLogin()
    {
        global $login;

        return isset($login) && $login == 'true'
            && (gc('status') != 'adm' || !logincheck());
    }

    /**
     * Returns whether the admin has just logged out.
     *
     * @return bool
     *
     * @access protected
     *
     * @global bool   Whether logout was requested.
     * @global string The current system function.
     */
    function isLogout()
    {
        global $logout, $f;

        if (version_compare($this->xhVersion(), '1.6dev', 'ge')) {
            return isset($f) && $f == 'xh_loggedout';
        } else {
            return $logout && $_COOKIE['status'] == 'adm' && logincheck();
        }
    }

    /**
     * Emits the script elements.
     *
     * @return void
     *
     * @access protected
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert at the bottom of the document.
     * @global string The (X)HTML to insert in the contents area.
     * @global bool   Whether we're in admin mode.
     *
     */
    function emitScripts()
    {
        global $pth, $bjs, $o, $adm;

        if ($adm) {
            $filename = $pth['folder']['plugins'] . 'keymaster/Keymaster.js';
            $js = $this->_views->js($filename);
            if (isset($bjs)) {
                $bjs .= $js;
            } else {
                $o .= $js;
            }
        }
    }

    /**
     * Handles the plugin administration.
     *
     * @return void
     *
     * @access protected
     *
     * @global
     */
    function administration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('off');
        switch ($admin) {
        case '':
            $o .= $this->_views->info($this->systemChecks());
            break;
        default:
            $o .= plugin_admin_common($action, $admin, 'keymaster');
        }
    }

    /**
     * Responds to a remaining time request.
     *
     * @return void
     *
     * @access protected
     *
     * @global bool Whether we're in admin mode.
     */
    function answerRemainingTime()
    {
        global $adm;

        if ($adm) {
            $remainingTime = $this->_model->remainingTime();
        } else {
            $remainingTime = -1;
        }
        header('Content-Type: text/plain');
        echo $remainingTime;
        exit;
    }

    /**
     * Denies the login.
     *
     * @return void
     *
     * @access protected
     *
     * @global string The (X)HTML to insert into the contents area.
     * @global string The current system function.
     * @global array  The localization of the plugins.
     */
    function denyLogin()
    {
        global $o, $f, $plugin_tx;

        $o .= $this->_views->message('fail', $plugin_tx['keymaster']['editing']);
        $f = '';
    }

    /**
     * Handles plugin related requests.
     *
     * @return void
     *
     * @access protected
     *
     * @global bool Whether we're in admin mode.
     * @global bool Whether the plugin administration is requested.
     */
    function dispatch()
    {
        global $adm, $keymaster;

        if ($this->wantsLogin() && !$this->_model->isFree()) {
            $this->denyLogin();
        } elseif ($this->isLogin()) {
            $this->login();
        } elseif ($this->isLogout()) {
            if (!$this->_model->take()) {
                e('cntwriteto', 'file', $this->_model->filename());
            }
        } elseif (isset($_GET['keymaster_time'])) {
            $this->answerRemainingTime();
        } elseif ($adm && isset($_POST['keymaster_reset'])) {
            $this->_model->reset();
            exit;
        } elseif ($adm && !$this->_model->sessionHasExpired()) {
            if (!$this->_model->reset()) {
                e('cntwriteto', 'file', $this->_model->filename());
            }
        } elseif ($adm) {
            $this->logout();
        }

        if ($adm && isset($keymaster) && $keymaster == 'true') {
            $this->administration();
        }
    }
}

?>
