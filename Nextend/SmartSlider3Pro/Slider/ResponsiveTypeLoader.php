<?php


namespace Nextend\SmartSlider3Pro\Slider;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Slider\ResponsiveType\ResponsiveTypeFactory;
use Nextend\SmartSlider3Pro\Slider\ResponsiveType\FullPage\ResponsiveTypeFullPage;

class ResponsiveTypeLoader {

    public function __construct() {

        Plugin::addAction('PluggableFactorySliderResponsiveType', array(
            $this,
            'sliderResponsiveTypes'
        ));
    }

    public function sliderResponsiveTypes() {
        ResponsiveTypeFactory::addType(new ResponsiveTypeFullPage());
    }
}