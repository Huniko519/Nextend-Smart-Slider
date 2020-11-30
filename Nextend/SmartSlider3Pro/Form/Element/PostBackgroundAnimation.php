<?php

namespace Nextend\SmartSlider3Pro\Form\Element;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\AbstractChooser;

class PostBackgroundAnimation extends AbstractChooser {

    protected function addScript() {

        Js::addInline('new N2Classes.FormElementPostAnimationManager("' . $this->fieldID . '", "postbackgroundanimationManager");');
    }
}