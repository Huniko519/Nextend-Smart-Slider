<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Visual;


use Nextend\Framework\Controller\Admin\AdminVisualManagerAjaxController;
use Nextend\SmartSlider3Pro\PostBackgroundAnimation\ModelPostBackgroundAnimation;

class ControllerAjaxPostBackgroundAnimation extends AdminVisualManagerAjaxController {

    protected $type = 'postbackgroundanimation';

    public function getModel() {

        return new ModelPostBackgroundAnimation($this);
    }

}