<?php

namespace Nextend\Framework\Parser;

use Nextend\Framework\Misc\Base64;

class Style {

    private $_style;

    public function __construct($style) {
        $decoded = $style;
        if ($decoded[0] != '{') {
            $decoded = Base64::decode($decoded);
        }

        $this->_style = json_decode($decoded, true);
    }

    public function printTab($tab = '') {
        $style = '';
        if (isset($this->_style[$tab])) {
            $tab   = &$this->_style[$tab];
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

    public function parse($property, $value) {
        $fn = 'parse' . $property;

        return $this->$fn($value);
    }

    public function parseBackgroundColor($v) {
        $hex = Color::hex82hex($v);
        if ($hex[1] == 'ff') {
            return 'background: #' . $hex[0] . ';';
        }

        $rgba = Color::hex2rgba($v);

        return 'background: RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
    }

    public function parsePadding($v) {
        $padding   = explode('|*|', $v);
        $unit      = array_pop($padding);
        $padding[] = '';

        return 'padding:' . implode($unit . ' ', $padding) . ';';
    }

    public function parseBoxShadow($v) {
        $boxShadow = explode('|*|', $v);

        if ($boxShadow[0] == '0' && $boxShadow[1] == '0' && $boxShadow[2] == '0' && $boxShadow[3] == '0') {
            return 'box-shadow: none;';
        } else {
            $rgba = Color::hex2rgba($boxShadow[4]);

            return 'box-shadow: ' . $boxShadow[0] . 'px ' . $boxShadow[1] . 'px ' . $boxShadow[2] . 'px ' . $boxShadow[3] . 'px RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
        }
    }

    public function parseBorder($v) {
        $border = explode('|*|', $v);
        $style  = 'border-width: ' . $border[0] . 'px';
        $style  .= 'border-style: ' . $border[1] . ';';
        $rgba   = Color::hex2rgba($border[2]);
        $style  .= 'border-color: #' . substr($border[2], 0, 6) . "; border-color: RGBA(" . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';

        return $style;
    }

    public function parseBorderRadius($v) {
        $radius   = explode('|*|', $v);
        $radius[] = '';

        return 'border-radius:' . $v . 'px;';
    }

    public function parseExtra($v) {
        return str_replace("\n", '', $v);
    }
}