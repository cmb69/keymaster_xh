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
    /** @var string*/
    private $filename;

    public function __construct(string $filename)
    {
        $this->filename = $filename;
    }

    public function filename(): string
    {
        return $this->filename;
    }

    public function mtime(): int
    {
        return filemtime($this->filename);
    }

    public function touch(): bool
    {
        return touch($this->filename);
    }

    public function size(): int
    {
        return filesize($this->filename);
    }

    public function purge(): bool
    {
        $stream = fopen($this->filename, 'w');
        if ($stream) {
            return fclose($stream);
        } else {
            return false;
        }
    }

    public function extend(): bool
    {
        $stream = fopen($this->filename, 'a');
        if ($stream) {
            $ok = (fwrite($stream, '*') !== false);
            fclose($stream);
        } else {
            $ok = false;
        }
        return $ok;
    }
}
