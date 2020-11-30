<?php


namespace Nextend\SmartSlider3Pro\Form\Element;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\AbstractField;
use Nextend\Framework\Form\ContainerInterface;
use Nextend\Framework\Form\Element\AbstractFieldHidden;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\TraitFieldset;

class AutoplayPicker extends AbstractFieldHidden implements ContainerInterface {

    use TraitFieldset;

    private static $separator = '|*|';

    protected function fetchElement() {
        $this->addAutoPlayPicker();

        $default = explode(self::$separator, $this->defaultValue);

        $value = explode(self::$separator, $this->getValue());

        $value = $value + $default;


        $html = '<div class="n2_field_autoplaypicker">';
        $html .= '<div class="n2_field_autoplaypicker__label"></div><i class="n2_field_autoplaypicker__arrow ssi_16 ssi_16--selectarrow"></i>';
        $html .= '<div class="n2_field_autoplaypicker__popover">';

        $subElements = array();
        $i           = 0;

        $element = $this->first;
        while ($element) {

            $element->setExposeName(false);
            if (isset($value[$i])) {
                $element->setDefaultValue($value[$i]);
            }

            $html            .= $this->decorateElement($element);
            $subElements[$i] = $element->getID();
            $i++;

            $element = $element->getNext();
        }

        $html .= '</div>';
        $html .= parent::fetchElement();
        $html .= '</div>';

        Js::addInline('new N2Classes.FormElementAutoPlayPicker("' . $this->fieldID . '", ' . json_encode($subElements) . ', "' . self::$separator . '");');

        return $html;
    }

    /**
     * @param AbstractField $element
     *
     * @return string
     */
    public function decorateElement($element) {

        return $this->parent->decorateElement($element);
    }

    protected function addAutoPlayPicker() {
        new Number($this, $this->name . '-1', n2_('Interval'), '', array(
            'wide' => 3,
            'min'  => 1
        ));

        new Select($this, $this->name . '-2', n2_('Interval modifier'), '', array(
            'options'            => array(
                'loop'       => n2_x('loops', 'Autoplay modifier'),
                'slide'      => n2_x('slide count', 'Autoplay modifier'),
                'slideindex' => n2_x('slide index', 'Autoplay modifier')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'loop'
                    ),
                    'field'  => array(
                        $this->getControlName() . $this->name . '-3'
                    )
                )
            ),
        ));
        new Select($this, $this->name . '-3', n2_('Stops on'), '', array(
            'options' => array(
                'current' => n2_x('last slide', 'Autoplay modifier'),
                'next'    => n2_x('next slide', 'Autoplay modifier')
            )
        ));
    }
}