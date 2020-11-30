<?php

namespace Nextend\Framework\Misc\Zip\Reader;

use Nextend\Framework\Misc\Zip\ReaderInterface;

class ZipExtension implements ReaderInterface {

    public function read($path) {
        $zip = zip_open($path);
        if (!is_resource($zip)) {
            return array();
        }
        $data = array();
        while ($entry = zip_read($zip)) {

            zip_entry_open($zip, $entry, "r");

            $this->recursiveRead($data, explode('/', zip_entry_name($entry)), zip_entry_read($entry, zip_entry_filesize($entry)));

            zip_entry_close($entry);
        }

        zip_close($zip);

        return $data;
    }

    private function recursiveRead(&$data, $parts, $content) {
        if (count($parts) == 1) {
            $data[$parts[0]] = $content;
        } else {
            if (!isset($data[$parts[0]])) {
                $data[$parts[0]] = array();
            }
            $this->recursiveRead($data[array_shift($parts)], $parts, $content);
        }
    }
}