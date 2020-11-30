<?php

namespace Nextend\SmartSlider3Pro\Form\Element;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\AbstractChooser;
use Nextend\Framework\Request\Request;

class ShapeDivider extends AbstractChooser {

    protected function addScript() {

        $MVCHelper = $this->getForm();

        Js::addInline('new N2Classes.FormElementShapeDividerManager("' . $this->fieldID . '", ' . json_encode(array(
                'editUrl' => $MVCHelper->createUrl(array(
                    'slider/shapedivider',
                    array(
                        'sliderid' => Request::$GET->getInt('sliderid')
                    )
                ), true)
            )) . ');');
    }
}