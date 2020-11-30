<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Accordion;


use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderType;

class SliderTypeAccordion extends AbstractSliderType {

    public function getName() {
        return 'accordion';
    }

    public function createFrontend($slider) {
        return new SliderTypeAccordionFrontend($slider);
    }

    public function createCss($slider) {
        return new SliderTypeAccordionCss($slider);
    }


    public function createAdmin() {
        return new SliderTypeAccordionAdmin($this);
    }

    public function export($export, $slider) {

        $export->addVisual($slider['params']->get('title-font', ''));
    }

    public function import($import, $slider) {

        $slider['params']->set('title-font', $import->fixSection($slider['params']->get('title-font', '')));
    }

    public function getItemDefaults() {

        return array(
            'align'  => 'left',
            'valign' => 'top'
        );
    }
}