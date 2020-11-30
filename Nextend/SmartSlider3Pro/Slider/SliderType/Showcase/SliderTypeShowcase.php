<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Showcase;


use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderType;

class SliderTypeShowcase extends AbstractSliderType {

    public function getName() {
        return 'showcase';
    }

    public function createFrontend($slider) {
        return new SliderTypeShowcaseFrontend($slider);
    }

    public function createCss($slider) {
        return new SliderTypeShowcaseCss($slider);
    }


    public function createAdmin() {
        return new SliderTypeShowcaseAdmin($this);
    }

    public function export($export, $slider) {
        $export->addImage($slider['params']->get('background', ''));
    }

    public function import($import, $slider) {

        $slider['params']->set('background', $import->fixImage($slider['params']->get('background', '')));
    }
}