<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Visual;


use Nextend\Framework\Controller\Admin\AdminVisualManagerAjaxController;
use Nextend\SmartSlider3Pro\SplitText\ModelSplitText;

class ControllerAjaxSplitTextAnimation extends AdminVisualManagerAjaxController {

    protected $type = 'splittextanimation';

    public function getModel() {

        return new ModelSplitText($this);
    }
}