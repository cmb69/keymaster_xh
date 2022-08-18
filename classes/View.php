<?php

/**
 * The views class.
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

class View
{
    /** @var string */
    private $templateFolder;

    /** @var array<string,string> */
    private $lang;

    /**
     * @param array<string,string> $lang
     */
    public function __construct(string $templateFolder, array $lang)
    {
        $this->templateFolder = $templateFolder;
        $this->lang = $lang;
    }

    public function text(string $key): string
    {
        return $this->lang[$key];
    }

    public function message(string $type, string $message): string
    {
        return XH_message($type, $message);
    }

    /**
     * @param array<string,mixed> $data
     */
    public function render(string $template, array $data): string
    {
        extract($data);
        ob_start();
        include "{$this->templateFolder}{$template}.php";
        return (string) ob_get_clean();
    }
}
