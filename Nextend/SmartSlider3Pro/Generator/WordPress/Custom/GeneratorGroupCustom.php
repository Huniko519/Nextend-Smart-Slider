<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\Custom;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\Custom\Sources\CustomCustom;

class GeneratorGroupCustom extends AbstractGeneratorGroup {

    protected $name = 'custom';

    public function getLabel() {
        return 'Custom';
    }

    public function getDescription() {
        return n2_('Creates slides by your custom settings.');
    }

    protected function loadSources() {
        $customGenerators = array();
        $customGenerators = apply_filters('smartslider3_custom_generator', $customGenerators);

        foreach ($customGenerators AS $customGenerator) {
            new CustomCustom($this, $customGenerator);
        }
    }
}
