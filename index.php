<?php

/**
 * Front-end of Keymaster_XH.
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
define('KEYMASTER_VERSION', '1.1');

/**
 * The key file class.
 */
require_once $pth['folder']['plugin_classes'] . 'Keyfile.php';

/**
 * The model class.
 */
require_once $pth['folder']['plugin_classes'] . 'Model.php';

/**
 * The views class.
 */
require_once $pth['folder']['plugin_classes'] . 'Views.php';

/**
 * The controller class.
 */
require_once $pth['folder']['plugin_classes'] . 'Controller.php';

/**
 * The controller object.
 *
 * @var Keymaster_Controller
 */
$_Keymaster = new Keymaster_Controller();

?>
