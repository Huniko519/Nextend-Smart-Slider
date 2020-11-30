<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Carousel;


use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderType;

class SliderTypeCarousel extends AbstractSliderType {

    public function getName() {
        return 'carousel';
    }

    public function createFrontend($slider) {
        return new SliderTypeCarouselFrontend($slider);
    }

    public function createCss($slider) {
        return new SliderTypeCarouselCss($slider);
    }


    public function createAdmin() {
        return new SliderTypeCarouselAdmin($this);
    }

    public function export($export, $slider) {
        $export->addImage($slider['params']->get('background', ''));
    }

    public function import($import, $slider) {

        $slider['params']->set('background', $import->fixImage($slider['params']->get('background', '')));
    }
}