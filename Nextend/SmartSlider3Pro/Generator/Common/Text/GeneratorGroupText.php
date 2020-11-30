<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Text;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Text\Sources\TextInput;
use Nextend\SmartSlider3Pro\Generator\Common\Text\Sources\TextText;

class GeneratorGroupText extends AbstractGeneratorGroup {

    protected $name = 'text';

    public function getLabel() {
        return 'CSV';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'CSV');
    }

    protected function loadSources() {

        new TextText($this, 'text', n2_('CSV from url'));
        new TextInput($this, 'input', n2_('CSV from input'));
    }
}
