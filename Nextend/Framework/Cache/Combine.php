<?php

namespace Nextend\Framework\Cache;

class Combine extends AbstractCache {

    protected $files = array();
    protected $inline = '';
    protected $fileType = '';
    protected $minify;
    protected $options = array();

    public function __construct($fileType, $minify = null, $options = array()) {
        $this->fileType          = $fileType;
        $this->minify            = $minify;
        $this->options           = $options;
        $this->options['minify'] = $this->minify;
        parent::__construct('combined', true);
    }

    public function add($file) {
        if (!in_array($file, $this->files)) {
            $this->files[] = $file;
        }
    }

    public function addInline($text) {
        $this->inline .= $text;
    }

    protected function getHash() {
        $hash = '';
        for ($i = 0; $i < count($this->files); $i++) {
            $hash .= $this->files[$i] . filemtime($this->files[$i]);
        }
        if (!empty($this->inline)) {
            $hash .= $this->inline;
        }

        return md5($hash . json_encode($this->options));
    }

    public function make() {
        $hash     = $this->getHash();
        $fileName = $hash . '.' . $this->fileType;
        if (!$this->exists($fileName)) {
            $buffer = '';
            for ($i = 0; $i < count($this->files); $i++) {
                $buffer .= file_get_contents($this->files[$i]);
            }
            if (is_callable($this->minify)) {
                $buffer = call_user_func($this->minify, $buffer);
            }
            $buffer .= $this->inline;

            $this->set($fileName, $buffer);
        }

        return $this->getPath($fileName);
    }
}