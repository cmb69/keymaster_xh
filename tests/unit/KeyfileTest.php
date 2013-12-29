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
    protected $filename;

    protected $keyfile;

    public function setUp()
    {
        vfsStreamWrapper::register();
        vfsStreamWrapper::setRoot(new vfsStreamDirectory('keyfile'));
        $this->filename = vfsStream::url('keyfile') . '/key';
        file_put_contents($this->filename, '');

        $this->keyfile = new Keymaster_Keyfile($this->filename);
    }

    public function testFilename()
    {
        $expected = $this->filename;
        $actual = $this->keyfile->filename();
        $this->assertEquals($expected, $actual);
    }

    public function testMtimeIsNotInTheFuture()
    {
        $actual = $this->keyfile->mtime();
        $this->assertTrue($actual <= time());
    }

    /**
     * @expectedException PHPUnit_Framework_Error_Warning
     */
    public function testTouchThrowsWarningWithOldVfs()
    {
        $actual = $this->keyfile->touch();
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
