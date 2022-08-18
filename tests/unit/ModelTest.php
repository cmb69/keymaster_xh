<?php

/**
 * A test case for the model class.
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package   Keymaster
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2013-2019 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */

namespace Keymaster;

use PHPUnit_Framework_TestCase;

/**
 * The keyfile class.
 */
require_once './classes/Keyfile.php';

/**
 * The class under test.
 */
require_once './classes/Model.php';

/**
 * A test case for the model class.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class ModelTest extends PHPUnit_Framework_TestCase
{
    protected $keyfile;

    protected $duration;

    public function setUp()
    {
        $this->keyfile = $this->createMock(Keyfile::class);
        $this->duration = 60;
        $this->model = new Model($this->keyfile, $this->duration);
    }

    public function testHasKeyAfterTaking()
    {
        $this->keyfile->expects($this->once())
            ->method('extend');
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(1));
        $this->model->take();
        $actual = $this->model->hasKey();
        $this->assertTrue($actual);
    }

    public function testNotHasKeyAfterGiving()
    {
        $this->keyfile->expects($this->once())
            ->method('purge');
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(0));
        $this->model->give();
        $actual = $this->model->hasKey();
        $this->assertFalse($actual);
    }

    public function testResetResetsLoggedInTime()
    {
        $this->keyfile->expects($this->once())
            ->method('touch');
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time()));
        $this->model->reset();
        $actual = $this->model->loggedInTime();
        $this->assertEquals(0, $actual);
    }

    public function testRemainingTimeIsDurationMinusLoggedInTime()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - 20));
        $expected = $this->duration - $this->model->loggedInTime();
        $actual = $this->model->remainingTime();
        $this->assertEquals($expected, $actual);
    }

    public function testSessionHasExpiredWhenRemainingTimeIsZero()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - $this->duration));
        $actual = $this->model->sessionHasExpired();
        $this->assertTrue($actual);
    }

    public function testFilenameEqualsKeyfilename()
    {
        $this->keyfile->expects($this->any())
            ->method('filename')
            ->will($this->returnValue('key'));
        $expected = $this->keyfile->filename();
        $actual = $this->model->filename();
        $this->assertEquals($expected, $actual);
    }

    public function testIsFreeIfHasKey()
    {
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(1));
        $actual = $this->model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsFreeIfSessionHasExpired()
    {
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time() - 2 * $this->duration));
        $actual = $this->model->isFree();
        $this->assertTrue($actual);
    }

    public function testIsNotFreeWhenHasNotKeyAndSessionHasNotExpired()
    {
        $this->keyfile->expects($this->any())
            ->method('size')
            ->will($this->returnValue(0));
        $this->keyfile->expects($this->any())
            ->method('mtime')
            ->will($this->returnValue(time()));
        $actual = $this->model->isFree();
        $this->assertFalse($actual);
    }

    public function testJsConfigHasRequiredElements()
    {
        global $plugin_cf;

        $plugin_cf = [
            'keymaster' => [
                'logout' => '1800',
                'warn' => '1200',
                'poll' => '7000',
            ]
        ];
        $actual = $this->model->jsConfig();
        $this->assertArrayHasKey('warn', $actual);
        $this->assertArrayHasKey('pollInterval', $actual);
    }

    public function testJsL10nIsPluginL10n()
    {
        global $plugin_tx;

        $plugin_tx = array(
            'keymaster' => array(
                'one' => '1',
                'two' => '2',
                'three' => '3'
            )
        );
        $expected = $plugin_tx['keymaster'];
        $actual = $this->model->jsL10n();
        $this->assertEquals($expected, $actual);
    }

    public function testPluginIconPath()
    {
        global $pth;

        $pth['folder']['plugins'] = './';
        $expected = './keymaster/keymaster.png';
        $actual = $this->model->pluginIconPath();
        $this->assertEquals($expected, $actual);
    }

    public function testStateIconPath()
    {
        global $pth;

        $state = 'warn';
        $pth['folder']['plugins'] = './';
        $expected = './keymaster/images/' . $state . '.png';
        $actual = $this->model->stateIconPath($state);
        $this->assertEquals($expected, $actual);
    }
}

?>
