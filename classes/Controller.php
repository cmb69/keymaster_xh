<?php

/**
 * The controller class.
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
 * The controller class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Controller
{
    /**
     * The model.
     *
     * @var Model
     */
    private $model;

    /**
     * The views.
     *
     * @var Views
     */
    private $views;

    /**
     * Initializes a new instance.
     *
     * @return void
     *
     * @global array The paths of system files and folders.
     * @global array The configuration of the plugins.
     */
    public function __construct()
    {
        global $pth, $plugin_cf;

        $filename = $pth['folder']['plugins'] . 'keymaster/key';
        $keyfile = new Keyfile($filename);
        $duration = $plugin_cf['keymaster']['logout'];
        $this->model = new Model($keyfile, $duration);
        $this->views = new Views($this->model);
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
     */
    private function xhVersion()
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
    private function baseUrl()
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
     */
    private function login()
    {
        if ($this->model->isFree()) {
            if ($this->model->give()) {
                // important, as filemtime() is called later for the same file:
                clearstatcache();
            } else {
                e('cntwriteto', 'file', $this->model->filename());
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
     * @global string The current page URL.
     */
    private function logout()
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
     * @global array  The paths of system files and folders.
     * @global array  The localization of the core.
     * @global array  The localization of the plugins.
     */
    private function systemChecks()
    {
        global $pth, $tx, $plugin_tx;

        $ptx = $plugin_tx['keymaster'];
        $phpVersion = '7.1.0';
        $xhVersion = '1.7.0';
        $checks = array();
        $checks[sprintf($ptx['syscheck_phpversion'], $phpVersion)]
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'ok' : 'fail';
        foreach (array('json') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'ok' : 'fail';
        }
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(substr(CMSIMPLE_XH_VERSION, strlen("CMSimple_XH ")), $xhVersion) >= 0 ? 'ok' : 'fail';
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
     * @global string The current system function.
     */
    private function wantsLogin()
    {
        global $f;

        return isset($f) && $f == 'login';
    }

    /**
     * Returns whether the admin has just logged in.
     *
     * @return bool
     *
     * @global string Whether login is requested.
     */
    private function isLogin()
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
     * @global bool   Whether logout was requested.
     * @global string The current system function.
     */
    private function isLogout()
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
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert at the bottom of the document.
     * @global string The (X)HTML to insert in the contents area.
     * @global bool   Whether we're in admin mode.
     */
    private function emitScripts()
    {
        global $pth, $bjs, $o, $adm;

        if ($adm) {
            $filename = $pth['folder']['plugins'] . 'keymaster/keymaster.js';
            $js = $this->views->js($filename);
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
     * @global string The (X)HTML to insert into the content area.
     * @global string The value of the <var>admin</var> parameter.
     * @global string The value of the <var>action</var> parameter.
     */
    private function administration()
    {
        global $o, $admin, $action;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= $this->views->info($this->systemChecks());
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
     * @global bool Whether we're in admin mode.
     */
    private function answerRemainingTime()
    {
        global $adm;

        if ($adm) {
            $remainingTime = $this->model->remainingTime();
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
     * @global string The (X)HTML to insert into the contents area.
     * @global string The current system function.
     * @global array  The localization of the plugins.
     */
    private function denyLogin()
    {
        global $o, $f, $plugin_tx;

        $o .= $this->views->message('fail', $plugin_tx['keymaster']['editing']);
        $f = '';
    }

    /**
     * Handles plugin related requests.
     *
     * @return void
     *
     * @global bool Whether we're in admin mode.
     * @global bool Whether the plugin administration is requested.
     */
    private function dispatch()
    {
        global $adm;

        if ($this->wantsLogin() && !$this->model->isFree()) {
            $this->denyLogin();
        } elseif ($this->isLogin()) {
            $this->login();
        } elseif ($this->isLogout()) {
            if (!$this->model->take()) {
                e('cntwriteto', 'file', $this->model->filename());
            }
        } elseif (isset($_GET['keymaster_time'])) {
            $this->answerRemainingTime();
        } elseif ($adm && isset($_POST['keymaster_reset'])) {
            $this->model->reset();
            exit;
        } elseif ($adm && !$this->model->sessionHasExpired()) {
            if (!$this->model->reset()) {
                e('cntwriteto', 'file', $this->model->filename());
            }
        } elseif ($adm) {
            $this->logout();
        }

        if ($adm && XH_wantsPluginAdministration('keymaster')) {
            $this->administration();
        }
    }
}
