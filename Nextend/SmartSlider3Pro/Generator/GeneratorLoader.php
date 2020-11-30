<?php


namespace Nextend\SmartSlider3Pro\Generator;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3Pro\Generator;

class GeneratorLoader {

    public function __construct() {

        Plugin::addAction('PluggableFactorySliderGenerator', array(
            $this,
            'sliderGenerator'
        ));
    }

    public function sliderGenerator() {
        new Generator\Common\GeneratorCommonLoader();
        new Generator\WordPress\GeneratorWordPressLoader();
    }
}