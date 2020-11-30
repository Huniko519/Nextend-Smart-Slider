<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Slider\License;


use Nextend\Framework\Controller\Admin\AdminAjaxController;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\SmartSlider3\Application\Admin\TraitAdminUrl;
use Nextend\SmartSlider3\Application\Model\ModelLicense;
use Nextend\SmartSlider3\SmartSlider3Info;

class ControllerAjaxLicense extends AdminAjaxController {

    use TraitAdminUrl;

    public function actionAdd() {
        $this->validateToken();
        $this->validatePermission('smartslider_edit');


        $licenseKey = Request::$REQUEST->getVar('licenseKey');
        if (empty($licenseKey)) {
            Notification::error(n2_('License key cannot be empty!'));
            $this->response->error();
        }


        $status = ModelLicense::getInstance()
                              ->checkKey($licenseKey, 'licenseadd');

        $hasError = SmartSlider3Info::hasApiError($status);

        if ($hasError == 'dashboard') {
            $this->response->redirect($this->getUrlDashboard());
        } else if ($hasError !== false) {
            $this->response->error();
        }

        ModelLicense::getInstance()
                    ->setKey($licenseKey);
        $this->response->respond(array(
            'valid' => true
        ));
    
    }

    public function actionCheck() {
        $this->validateToken();
        $showErrors = Request::$REQUEST->getInt('showErrors', 1);

        $status = ModelLicense::getInstance()
                              ->isActive(Request::$REQUEST->getInt('cacheAccepted', 1));

        if ($showErrors) {
            $hasError = SmartSlider3Info::hasApiError($status);
            if ($hasError == 'dashboard') {
                $this->response->redirect($this->getUrlDashboard());
            } else if ($hasError !== false) {
                $this->response->error();
            }
            Notification::notice(n2_('License key is active!'));
            $this->response->respond();
        }

        if ($status == 'OK') {
            $this->response->respond();
        }
        $this->response->error();
    
    }
}