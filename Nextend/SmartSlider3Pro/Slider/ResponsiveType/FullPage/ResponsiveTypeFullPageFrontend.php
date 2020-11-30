<?php

namespace Nextend\SmartSlider3Pro\Slider\ResponsiveType\FullPage;

use Nextend\SmartSlider3\Slider\ResponsiveType\AbstractResponsiveTypeFrontend;

class ResponsiveTypeFullPageFrontend extends AbstractResponsiveTypeFrontend {

    public function parse($params, $responsive, $features) {
        $responsive->scaleDown = 1;
        $responsive->scaleUp   = 1;

        $features->align->align = 'normal';

        $responsive->forceFull          = intval($params->get('responsiveForceFull', 1));
        $responsive->forceFullOverflowX = $params->get('responsiveForceFullOverflowX', 'body');

        $responsive->forceFullHorizontalSelector = $params->get('responsiveForceFullHorizontalSelector', 'body');
        $responsive->constrainRatio              = intval($params->get('responsiveConstrainRatio', 0));

        $responsive->sliderHeightBasedOn            = $params->get('sliderHeightBasedOn', 'real');
        $responsive->responsiveDecreaseSliderHeight = intval($params->get('responsiveDecreaseSliderHeight', 0));
    }
}