<?php

namespace Nextend\SmartSlider3Pro\Form\Element;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\AbstractChooser;
use Nextend\Framework\Request\Request;

class Particle extends AbstractChooser {

    protected function addScript() {

        $MVCHelper = $this->getForm();

        Js::addInline('new N2Classes.FormElementParticleManager("' . $this->fieldID . '", ' . json_encode(array(
                'editUrl' => $MVCHelper->createUrl(array(
                    'slider/particle',
                    array(
                        'sliderid' => Request::$GET->getInt('sliderid')
                    )
                ), true)
            )) . ');');
    }
}