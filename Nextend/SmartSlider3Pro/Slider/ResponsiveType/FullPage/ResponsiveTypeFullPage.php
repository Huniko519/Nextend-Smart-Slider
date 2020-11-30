<?php

namespace Nextend\SmartSlider3Pro\Slider\ResponsiveType\FullPage;

use Nextend\SmartSlider3\Slider\ResponsiveType\AbstractResponsiveType;

class ResponsiveTypeFullPage extends AbstractResponsiveType {


    public function getName() {
        return 'fullpage';
    }

    public function createFrontend($responsive) {

        return new ResponsiveTypeFullPageFrontend($this, $responsive);
    }

    public function createAdmin() {

        return new ResponsiveTypeFullPageAdmin($this);
    }


}