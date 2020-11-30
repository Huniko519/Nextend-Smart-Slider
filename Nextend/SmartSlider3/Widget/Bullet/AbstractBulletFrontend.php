<?php


namespace Nextend\SmartSlider3\Widget\Bullet;


use Nextend\Framework\Platform\Platform;
use Nextend\Framework\Url\Url;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

abstract class AbstractBulletFrontend extends AbstractWidgetFrontend {


    public function getCommonAssetsUri() {
        return Url::pathToUri($this->getCommonAssetsPath());
    }

    public function getCommonAssetsPath() {

        return Platform::filterAssetsPath(dirname(__FILE__) . '/Assets');
    }
}