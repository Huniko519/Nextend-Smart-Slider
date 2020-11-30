<?php


namespace Nextend\SmartSlider3Pro\Application\Admin;


use Nextend\Framework\Pattern\GetAssetsPathTrait;
use Nextend\Framework\Plugin;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Application\Admin\ApplicationTypeAdmin;
use Nextend\SmartSlider3\Application\Admin\Slider\ControllerAjaxSlider;
use Nextend\SmartSlider3\Application\Admin\Slider\ControllerSlider;
use Nextend\SmartSlider3\Application\Admin\Sliders\ControllerAjaxSliders;
use Nextend\SmartSlider3Pro\Application\Admin\Slider\License\ControllerAjaxLicense;
use Nextend\SmartSlider3Pro\Application\Admin\Slider\License\ControllerLicense;
use Nextend\SmartSlider3Pro\Application\Admin\Slider\PluggedControllerAjaxSlider;
use Nextend\SmartSlider3Pro\Application\Admin\Slider\PluggedControllerSlider;
use Nextend\SmartSlider3Pro\Application\Admin\Sliders\PluggedControllerAjaxSliders;
use Nextend\SmartSlider3Pro\Application\Admin\Visual\ControllerAjaxPostBackgroundAnimation;
use Nextend\SmartSlider3Pro\Application\Admin\Visual\ControllerAjaxSplitTextAnimation;

class PluggedApplicationTypeAdmin {

    use GetAssetsPathTrait;

    /** @var ApplicationTypeAdmin */
    protected $applicationType;

    /**
     * PluggedApplicationTypeAdmin constructor.
     *
     * @param ApplicationTypeAdmin $applicationType
     */
    public function __construct($applicationType) {

        $this->applicationType = $applicationType;

        ResourceTranslator::createResource('$ss3-pro-admin$', self::getAssetsPath(), self::getAssetsUri());

        $applicationType->addExternalController('postbackgroundanimation', $this);

        $applicationType->addExternalController('splittextanimation', $this);

        $applicationType->addExternalController('license', $this);

        Plugin::addAction('PluggableController\Nextend\SmartSlider3\Application\Admin\Sliders\ControllerAjaxSliders', array(
            $this,
            'plugControllerAjaxSliders'
        ));

        Plugin::addAction('PluggableController\Nextend\SmartSlider3\Application\Admin\Slider\ControllerSlider', array(
            $this,
            'plugControllerSlider'
        ));

        Plugin::addAction('PluggableController\Nextend\SmartSlider3\Application\Admin\Slider\ControllerAjaxSlider', array(
            $this,
            'plugControllerAjaxSlider'
        ));
    }

    public function getControllerAjaxPostBackgroundAnimation() {

        return new ControllerAjaxPostBackgroundAnimation($this->applicationType);
    }

    public function getControllerAjaxSplitTextAnimation() {

        return new ControllerAjaxSplitTextAnimation($this->applicationType);
    }

    public function getControllerLicense() {

        return new ControllerLicense($this->applicationType);
    }

    public function getControllerAjaxLicense() {

        return new ControllerAjaxLicense($this->applicationType);
    }

    /**
     * @param ControllerAjaxSliders $controller
     */
    public function plugControllerAjaxSliders($controller) {

        new PluggedControllerAjaxSliders($controller);
    }

    /**
     * @param ControllerSlider $controller
     */
    public function plugControllerSlider($controller) {

        new PluggedControllerSlider($controller);
    }

    /**
     * @param ControllerAjaxSlider $controller
     */
    public function plugControllerAjaxSlider($controller) {

        new PluggedControllerAjaxSlider($controller);
    }
}