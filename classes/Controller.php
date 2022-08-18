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
        $this->views = new Views();
        $this->emitScripts();
        $this->dispatch();
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
        unset($_SESSION['xh_password'][CMSIMPLE_ROOT]);
        header('Location: ' . CMSIMPLE_URL . '?' . $su, true, 303);
        exit;
    }

    /**
     * Returns an array with system checks.
     *
     * @return array<string,string>
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
            = version_compare(PHP_VERSION, $phpVersion) >= 0 ? 'xh_success' : 'xh_fail';
        foreach (array('json') as $ext) {
            $checks[sprintf($ptx['syscheck_extension'], $ext)]
                = extension_loaded($ext) ? 'xh_success' : 'xh_fail';
        }
        $checks[sprintf($ptx['syscheck_xhversion'], $xhVersion)]
            = version_compare(substr(CMSIMPLE_XH_VERSION, strlen("CMSimple_XH ")), $xhVersion) >= 0
                ? 'xh_success'
                : 'xh_fail';
        $folders = array();
        foreach (array('config/', 'css/', 'languages/') as $folder) {
            $folders[] = $pth['folder']['plugins'] . 'keymaster/' . $folder;
        }
        foreach ($folders as $folder) {
            $checks[sprintf($ptx['syscheck_writable_folder'], $folder)]
                = is_writable($folder) ? 'xh_success' : 'xh_warning';
        }
        $file = $pth['folder']['plugins'] . 'keymaster/key';
        $checks[sprintf($ptx['syscheck_writable_file'], $file)]
            = is_writable($file) ? 'xh_success' : 'xh_fail';
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
        global $f;

        return isset($f) && $f == 'xh_loggedout';
    }

    /**
     * Emits the script elements.
     *
     * @return void
     *
     * @global array  The paths of system files and folders.
     * @global string The (X)HTML to insert at the bottom of the document.
     * @global bool   Whether we're in admin mode.
     */
    private function emitScripts()
    {
        global $pth, $bjs, $adm;

        if ($adm) {
            $filename = $pth['folder']['plugins'] . 'keymaster/keymaster.js';
            $bjs .= $this->js($filename);
        }
    }


    /**
     * Returns the script elements.
     *
     * @param string $filename A JS script filename.
     *
     * @return string (X)HTML.
     */
    private function js($filename)
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
        global $o, $admin;

        $o .= print_plugin_admin('off');
        switch ($admin) {
            case '':
                $o .= $this->views->info($this->systemChecks());
                break;
            default:
                $o .= plugin_admin_common();
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

        if ($adm) {
            XH_registerStandardPluginMenuItems(false);
            if (XH_wantsPluginAdministration('keymaster')) {
                $this->administration();
            }
        }
    }
}
