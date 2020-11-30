<?php

namespace Nextend\Framework\Form\Element;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\View\Html;

class FloatToPercent extends AbstractFieldHidden {

    protected $class = 'n2_field_number ';

    protected $min = '';

    protected $max = '';

    protected $sublabel = '';

    protected $fieldType = 'text';

    protected $unit = '%';

    protected $style = 'width:32px;';

    protected function fetchElement() {

        if ($this->min == '') {
            $this->min = '-Number.MAX_VALUE';
        }

        if ($this->max == '') {
            $this->max = 'Number.MAX_VALUE';
        }

        Js::addInline('new N2Classes.FormElementFloatToPercent("' . $this->fieldID . '", ' . $this->min . ', ' . $this->max . ');');

        $html = Html::openTag('div', array(
            'class' => 'n2_field_text ' . $this->getClass()
        ));

        $html .= Html::tag('input', array(
            'type'         => $this->fieldType,
            'id'           => $this->fieldID . '-input',
            'value'        => $this->getValue() * 100,
            'style'        => $this->getStyle(),
            'autocomplete' => 'off'
        ), false);

        if ($this->unit) {
            $html .= Html::tag('div', array(
                'class' => 'n2_field_number__unit'
            ), $this->unit);
        }
        $html .= parent::fetchElement();
        $html .= "</div>";

        return $html;
    }

    public function setMin($min) {
        $this->min = $min;
    }

    /**
     * @param int $max
     */
    public function setMax($max) {
        $this->max = $max;
    }
}