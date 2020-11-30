<?php

namespace Nextend\SmartSlider3Pro;

use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3Pro\Application\PluggedApplicationSmartSlider3Pro;
use Nextend\SmartSlider3Pro\Generator\GeneratorLoader;
use Nextend\SmartSlider3Pro\Renderable\Item\ItemLoader;
use Nextend\SmartSlider3Pro\Slider\ResponsiveTypeLoader;
use Nextend\SmartSlider3Pro\Slider\SliderTypeLoader;
use Nextend\SmartSlider3Pro\Widget\WidgetLoader;

class SmartSlider3Pro {

    use SingletonTrait;

    protected function init() {

        Plugin::addAction('PluggableApplication\Nextend\SmartSlider3\Application\ApplicationSmartSlider3', array(
            $this,
            'plugSmartSlider3Pro'
        ));

        new SliderTypeLoader();

        new ResponsiveTypeLoader();

        new WidgetLoader();

        new GeneratorLoader();

        new ItemLoader();
    
    }

    /**
     * @param ApplicationSmartSlider3 $application
     */
    public function plugSmartSlider3Pro($application) {

        new PluggedApplicationSmartSlider3Pro($application);
    }
}