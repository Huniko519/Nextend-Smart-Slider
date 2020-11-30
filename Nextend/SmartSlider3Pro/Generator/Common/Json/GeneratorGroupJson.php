<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Json;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Json\Sources\JsonInput;
use Nextend\SmartSlider3Pro\Generator\Common\Json\Sources\JsonUrl;

class GeneratorGroupJson extends AbstractGeneratorGroup {

    protected $name = 'json';

    public function getLabel() {
        return 'JSON';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'JSON');
    }

    protected function loadSources() {

        new JsonUrl($this, 'url', n2_('JSON from url'));
        new JsonInput($this, 'input', n2_('JSON from input'));
    }
}