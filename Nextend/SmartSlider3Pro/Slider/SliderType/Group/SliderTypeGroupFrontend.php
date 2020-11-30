<?php

namespace Nextend\SmartSlider3Pro\Slider\SliderType\Group;

use Nextend\Framework\Platform\Platform;
use Nextend\SmartSlider3\Application\Model\ModelSlidersXRef;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;
use Nextend\SmartSlider3\SliderManager\SliderManager;

class SliderTypeGroupFrontend extends AbstractSliderTypeFrontend {

    private $earlier = 2145916800;

    public function render($css) {

        ob_start();
        $this->renderType($css);


        return ob_get_clean();
    }

    protected function renderType($css) {
        $isAdmin = Platform::isAdmin();

        $xref = new ModelSlidersXRef($this->slider);
        $rows = $xref->getSliders($this->slider->data->get('id'), 'published');
        foreach ($rows as $row) {
            $slider     = new SliderManager($this->slider, $row['slider_id'], $isAdmin);
            $sliderHTML = $slider->render();
            echo $sliderHTML;
            if (!empty($sliderHTML)) {
                $this->earlier = min($slider->slider->getNextCacheRefresh(), $this->earlier);
            }
        }
    }

    public function getNextCacheRefresh() {
        return $this->earlier;
    }
}