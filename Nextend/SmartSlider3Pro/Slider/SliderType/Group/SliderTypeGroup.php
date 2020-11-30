<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Group;


use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderType;

class SliderTypeGroup extends AbstractSliderType {

    public function getName() {
        return 'group';
    }

    public function createFrontend($slider) {
        return new SliderTypeGroupFrontend($slider);
    }

    public function createCss($slider) {
        return new SliderTypeGroupCss($slider);
    }

    public function createAdmin() {
        return false;
    }
}