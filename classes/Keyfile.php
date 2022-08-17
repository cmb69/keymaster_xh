<?php

/**
 * The key file class.
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
 * The key file class.
 *
 * Encapsulates the necessary file system access.
 *
 * @category CMSimple_XH
 * @package  Keymaster
 * @author   Christoph M. Becker <cmbecker69@gmx.de>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://3-magi.net/?CMSimple_XH/Keymaster_XH
 */
class Keyfile
{
    /**
     * The filename of the key file.
     *
     * @var string
     */
    private $_filename;

    /**
     * Initializes a new instance.
     *
     * @param string $filename A filename.
     *
     * @return void
     */
    public function __construct($filename)
    {
        $this->_filename = $filename;
    }

    /**
     * Returns the filename of the key file.
     *
     * @return string
     */
    public function filename()
    {
        return $this->_filename;
    }

    /**
     * Returns the timestamp of the last modification of the key file.
     *
     * @return string
     */
    public function mtime()
    {
        return filemtime($this->_filename);
    }

    /**
     * Sets the timestamp of the last modification of the key file to now,
     * and returns whether that succeeded.
     *
     * @return bool
     */
    public function touch()
    {
        return touch($this->_filename);
    }

    /**
     * Returns the size of the key file in bytes.
     *
     * @return int
     */
    public function size()
    {
        return filesize($this->_filename);
    }

    /**
     * Purges the contents of the key file, and returns whether that succeeded.
     *
     * @return bool
     */
    public function purge()
    {
        $stream = fopen($this->_filename, 'w');
        if ($stream) {
            return fclose($stream);
        } else {
            return false;
        }
    }

    /**
     * Extends the key file by a single aterix (*), and returns whether that
     * succeeded.
     *
     * @return bool
     */
    public function extend()
    {
        $stream = fopen($this->_filename, 'a');
        if ($stream) {
            $ok = (fwrite($stream, '*') !== false);
            fclose($stream);
        } else {
            $ok = false;
        }
        return $ok;
    }
}

?>
