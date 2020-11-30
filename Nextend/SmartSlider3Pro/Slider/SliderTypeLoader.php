<?php


namespace Nextend\SmartSlider3Pro\Slider;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Slider\SliderType\SliderTypeFactory;
use Nextend\SmartSlider3Pro\Slider\SliderType\Accordion\SliderTypeAccordion;
use Nextend\SmartSlider3Pro\Slider\SliderType\Carousel\SliderTypeCarousel;
use Nextend\SmartSlider3Pro\Slider\SliderType\Group\SliderTypeGroup;
use Nextend\SmartSlider3Pro\Slider\SliderType\Showcase\SliderTypeShowcase;

class SliderTypeLoader {

    public function __construct() {

        Plugin::addAction('PluggableFactorySliderType', array(
            $this,
            'sliderTypes'
        ));
    }

    public function sliderTypes() {
        SliderTypeFactory::addType(new SliderTypeGroup());
        SliderTypeFactory::addType(new SliderTypeAccordion());
        SliderTypeFactory::addType(new SliderTypeShowcase());
        SliderTypeFactory::addType(new SliderTypeCarousel());
    }
}