<?php


namespace Nextend\SmartSlider3Pro\Application\Frontend;


use Nextend\Framework\Pattern\GetAssetsPathTrait;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend;

class PluggedApplicationTypeFrontend {

    use GetAssetsPathTrait;

    /** @var ApplicationTypeFrontend */
    protected $applicationType;

    public function __construct($applicationType) {

        $this->applicationType = $applicationType;

        ResourceTranslator::createResource('$ss3-pro-frontend$', self::getAssetsPath(), self::getAssetsUri());
    }
}