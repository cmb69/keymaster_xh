<?php

/**
 *
 *
 * PHP version 5
 *
 * @category  CMSimple_XH
 * @package
 * @author    Christoph M. Becker <cmbecker69@gmx.de>
 * @copyright 2011-2013 Christoph M. Becker <http://3-magi.net/>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @version   SVN: $Id$
 * @link
 */

require_once 'vfsStream/vfsStream.php';

require_once './classes/Keyfile.php';

/**
 *
 *
 * @category CMSimple_XH
 * @package
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link
 */
class KeyfileTest extends PHPUnit_Framework_TestCase
{
    protected $basePath;

    protected $keyfile;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('keyfile'));
        $this->basePath = vfsStream::url('keyfile') . '/';
        $filename = $this->basePath . '/key';
        file_put_contents($filename, '');

        $this->keyfile = new Keymaster_Keyfile($filename);
    }

    public function testExtendIncreasesSizeByOne()
    {
        $expected = $this->keyfile->size() + 1;
        $actual = $this->keyfile->extend();
        $this->assertTrue($actual);
        clearstatcache();
        $actual = $this->keyfile->size();
        $this->assertEquals($expected, $actual);
    }

    public function testPurgeSetsFilesizeToZero()
    {
        $expected = 0;

        $actual = $this->keyfile->extend();
        $this->assertTrue($actual);

        $actual = $this->keyfile->purge();
        $this->assertTrue($actual);

        $actual = $this->keyfile->size();
        $this->assertEquals($expected, $actual);
    }
}

?>
