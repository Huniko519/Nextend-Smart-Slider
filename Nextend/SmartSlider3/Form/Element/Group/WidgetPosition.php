<?php


namespace Nextend\SmartSlider3\Form\Element\Group;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Unit;
use Nextend\Framework\Localization\Localization;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Form\Element\WidgetArea;

class WidgetPosition extends Grouping {

    protected $rowClass = '';

    protected function fetchElement() {
        $mode = new OnOff($this, $this->name . '-mode', n2_('Advanced'), 'simple');
        $mode->setCustomValues('simple', 'advanced');
        $mode->setRelatedFieldsOff(array(
            'slider' . $this->name . '-simple'
        ));
        $mode->setRelatedFieldsOn(array(
            'slider' . $this->name . '-advanced'
        ));
    

        $this->addSimple();
        $this->addAdvanced();
    

        Js::addInline('new N2Classes.FormElementWidgetPosition("' . $this->fieldID . '");');

        $html = '';

        $element = $this->first;
        while ($element) {

            $html .= $this->decorateElement($element);

            $element = $element->getNext();
        }

        return Html::tag('div', array(
            'id'    => $this->fieldID,
            'class' => 'n2_field_widget_position'
        ), Html::tag('div', array(
                'class' => 'n2_field_widget_position__label'
            ), '') . '<i class="n2_field_widget_position__arrow ssi_16 ssi_16--selectarrow"></i>' . Html::tag('div', array(
                'class' => 'n2_field_widget_position__popover'
            ), $html));
    }

    protected function addSimple() {

        $simple = new Grouping($this, $this->name . '-simple');

        new WidgetArea($simple, $this->name . '-area', false);
        new Select($simple, $this->name . '-stack', n2_('Stack'), 1, array(
            'options' => array(
                1 => 1,
                2 => 2,
                3 => 3,
                4 => 4,
                5 => 5
            )
        ));
        new Number($simple, $this->name . '-offset', n2_('Offset'), 0, array(
            'wide' => 4,
            'unit' => 'px'
        ));
    }

    protected function addAdvanced() {
        $advanced = new Grouping($this, $this->name . '-advanced');

        new Select($advanced, $this->name . '-horizontal', n2_('Horizontal'), 'left', array(
            'options' => array(
                'left'  => n2_('Left'),
                'right' => n2_('Right')
            )
        ));

        $horizontalPosition = new Text($advanced, $this->name . '-horizontal-position', n2_('Position'), 0, array(
            'style' => 'width:100px;'
        ));

        new Unit($horizontalPosition, $this->name . '-horizontal-unit', false, 'px', array(
            'units' => array(
                'px',
                '%'
            )
        ));

        new Select($advanced, $this->name . '-vertical', n2_('Vertical'), 'top', array(
            'options' => array(
                'top'    => n2_('Top'),
                'bottom' => n2_('Bottom')
            )
        ));

        $verticalPosition = new Text($advanced, $this->name . '-vertical-position', n2_('Position'), 0, array(
            'style' => 'width:100px;'
        ));

        new Unit($verticalPosition, $this->name . '-vertical-unit', false, 'px', array(
            'units' => array(
                'px',
                '%'
            )
        ));
    
    }
}