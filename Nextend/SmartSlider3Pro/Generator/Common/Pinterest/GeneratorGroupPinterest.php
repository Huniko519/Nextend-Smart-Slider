<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Pinterest;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Pinterest\Sources\PinterestImages;

class GeneratorGroupPinterest extends AbstractGeneratorGroup {

    protected $name = 'pinterest';

    public function getLabel() {
        return 'Pinterest';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Pinterest images');
    }

    protected function loadSources() {

        new PinterestImages($this, 'images', n2_('Images'));
    }

}