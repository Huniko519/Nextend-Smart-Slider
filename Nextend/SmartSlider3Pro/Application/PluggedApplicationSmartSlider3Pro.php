<?php


namespace Nextend\SmartSlider3Pro\Application;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Application\Admin\ApplicationTypeAdmin;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend;
use Nextend\SmartSlider3Pro\Application\Admin\PluggedApplicationTypeAdmin;
use Nextend\SmartSlider3Pro\Application\Frontend\PluggedApplicationTypeFrontend;

class PluggedApplicationSmartSlider3Pro {

    /** @var ApplicationSmartSlider3 */
    protected $application;

    /**
     * PluggedApplicationSmartSlider3Pro constructor.
     *
     * @param ApplicationSmartSlider3 $application
     */
    public function __construct($application) {

        $this->application = $application;

        Plugin::addAction('PluggableApplicationType\Nextend\SmartSlider3\Application\Admin\ApplicationTypeAdmin', array(
            $this,
            'plugApplicationTypeAdmin'
        ));

        Plugin::addAction('PluggableApplicationType\Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend', array(
            $this,
            'plugApplicationTypeFrontend'
        ));
    }


    /**
     * @param ApplicationTypeAdmin $applicationTypeAdmin
     */
    public function plugApplicationTypeAdmin($applicationTypeAdmin) {

        new PluggedApplicationTypeAdmin($applicationTypeAdmin);
    }


    /**
     * @param ApplicationTypeFrontend $applicationTypeFrontend
     */
    public function plugApplicationTypeFrontend($applicationTypeFrontend) {

        new PluggedApplicationTypeFrontend($applicationTypeFrontend);
    }
}