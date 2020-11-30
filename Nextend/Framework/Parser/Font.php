<?php

namespace Nextend\Framework\Parser;

use Nextend\Framework\Asset\Fonts\Google\Google;

class Font {

    /**
     * @var array
     */
    private $_font;

    public function __construct($font) {
        $this->_font = json_decode($font, true);
    }

    /**
     * @param string $tab
     *
     * @return string
     */
    public function printTab($tab = '') {
        if ($tab == '') $tab = $this->_font['firsttab'];
        $style = '';
        if (isset($this->_font[$tab])) {
            $tab   = &$this->_font[$tab];
            $extra = '';
            if (isset($tab['extra'])) {
                $extra = $tab['extra'];
                unset($tab['extra']);
            }
            foreach ($tab AS $k => $v) {
                $style .= $this->parse($k, $v);
            }
            $style .= $this->parse('extra', $extra);
        }

        return $style;
    }

    /**
     * @param        $target
     * @param string $source
     */
    public function mixinTab($target, $source = '') {
        if ($source == '') $source = $this->_font['firsttab'];
        $this->_font[$target] = array_merge($this->_font[$source], $this->_font[$target]);
    }

    /**
     * @param $property
     * @param $value
     *
     * @return mixed
     */
    public function parse($property, $value) {
        $fn = 'parse' . $property;

        return $this->$fn($value);
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseColor($v) {

        $hex = Color::hex82hex($v);

        if ($hex[1] == 'ff') {
            return 'color: #' . $hex[0] . ';';
        }

        $rgba = Color::hex2rgba($v);

        return 'color: RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseSize($v) {
        return 'font-size:' . Common::parse($v, '') . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseTShadow($v) {
        $v    = Common::parse($v);
        $rgba = Color::hex2rgba($v[3]);
        if ($v[0] == 0 && $v[1] == 0 && $v[2] == 0) return 'text-shadow: none;';

        return 'text-shadow: ' . $v[0] . 'px ' . $v[1] . 'px ' . $v[2] . 'px RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseAfont($v) {
        return 'font-family: ' . $this->loadFont($v) . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseLineHeight($v) {
        if ($v == '') return '';

        return 'line-height: ' . $v . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseBold($v) {
        if ($v == '1') return 'font-weight: bold;';

        return 'font-weight: normal;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseItalic($v) {
        if ($v == '1') return 'font-style: italic;';

        return 'font-style: normal;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseUnderline($v) {
        if ($v == '1') return 'text-decoration: underline;';

        return 'text-decoration: none;';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parsePaddingLeft($v) {
        return '';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseAlign($v) {
        return 'text-align: ' . $v . ';';
    }

    /**
     * @param $v
     *
     * @return string
     */
    public function parseReset($v) {
        return '';
    }

    public function parseExtra($v) {
        return $v;
    }

    /**
     * @param $families
     *
     * @return mixed
     */
    public function loadFont($families) {
        preg_match_all("/google\(.*?family=(.*?)\);\)/", $families, $out, PREG_SET_ORDER);
        foreach ($out AS $f) {
            preg_match('/(.*?)(:(.*?))?(&subset=(.*))?$/', $f[1], $g);
            $family = str_replace('+', ' ', $g[1]);
            $styles = 400;
            if (isset($g[3]) && !empty($g[3])) {
                $styles = $g[3];
            }
            $subset = 'latin';
            if (isset($g[5])) {
                $subset = $g[5];
            }
            Google::addSubset($subset);
            foreach (explode(',', $styles) AS $style) {
                Google::addFont($family, $style);
            }
            $families = str_replace($f[0], "'" . $family . "'", $families);
        }

        return $families;
    }
}