<?php

/**
 * A test case for the views class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2015 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

require_once './classes/Model.php';

require_once './classes/Views.php';

define('KEYMASTER_VERSION', 'test');

/**
 * A test case for the views class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class ViewsTest extends PHPUnit_Framework_TestCase
{
    private $_views;

    public function setUp()
    {
        $model = $this->getMockBuilder('Keymaster_Model')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_views = new Keymaster_Views($model);
    }

    public function testMessage()
    {
        $message = 'foobar';
        $actual = $this->_views->message('fail', $message);
        $matcher = array(
            'tag' => 'p',
            'attributes' => array('class' => 'cmsimplecore_warning'),
            'content' => $message
        );
        @$this->assertTag($matcher, $actual);
    }

    public function testInfo()
    {
        $matcher = array('tag' => 'h4');
        $actual = $this->_views->info(array('a check' => 'ok'));
        @$this->assertTag($matcher, $actual);
    }

    public function testJs()
    {
        $matcher = array(
            'tag' => 'script',
            'attributes' => array('src' => 'foobar')
        );
        $actual = $this->_views->js('foobar');
        @$this->assertTag($matcher, $actual);
    }
}

?>
