<?php

namespace Nextend\SmartSlider3Pro\Form\Element;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\AbstractFieldHidden;
use Nextend\Framework\View\Html;

class CanvasLayerParentPicker extends AbstractFieldHidden {

    protected function fetchElement() {

        Js::addInline('new N2Classes.FormElementLayerPicker("' . $this->fieldID . '");');
        $this->renderRelatedFields();

        return parent::fetchElement() . Html::tag('div', array(
                'class' => 'n2_ss_absolute_parent_picker'
            ), '<i class="ssi_16"></i>');
    }
}