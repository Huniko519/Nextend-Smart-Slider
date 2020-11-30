<?php

namespace Nextend\Framework\Request\Parser;

class WordPressRequestParser extends AbstractRequestParser {

    public function parseData($data) {
        if (is_array($data)) {
            return $this->stripslashesRecursive($data);
        }

        return stripslashes($data);
    }

    private function stripslashesRecursive($array) {
        foreach ($array as $key => $value) {
            $array[$key] = is_array($value) ? $this->stripslashesRecursive($value) : stripslashes($value);
        }

        return $array;
    }
}