<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Slider\License;


use Nextend\SmartSlider3\Application\Admin\AbstractControllerAdmin;
use Nextend\SmartSlider3\Application\Model\ModelLicense;
use Nextend\SmartSlider3\SmartSlider3Info;

class ControllerLicense extends AbstractControllerAdmin {

    public function actionDeAuthorize() {
        $status = ModelLicense::getInstance()
                              ->deAuthorize();

        SmartSlider3Info::hasApiError($status);

        $this->redirectToSliders();
    
    }
}