<?php

namespace Nextend\SmartSlider3Pro\Form\Element\Select;


use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\View\Html;

class ShapeDividerSelect extends Select {

    private static $_options;

    public function __construct($insertAt, $name = '', $label = '', $default = '', array $parameters = array()) {
        if (self::$_options === null) {
            self::$_options = array(
                'simple-Arrow'        => n2_('Arrow'),
                'simple-Curve1'       => n2_('Curve 1'),
                'simple-Curve2'       => n2_('Curve 2'),
                'simple-Curve3'       => n2_('Curve 3'),
                'simple-Curve4'       => n2_('Curve 4'),
                'simple-Curves'       => n2_('Curves'),
                'simple-Fan1'         => n2_('Fan 1'),
                'simple-Fan2'         => n2_('Fan 2'),
                'simple-Fan3'         => n2_('Fan 3'),
                'simple-Hills'        => n2_('Hills'),
                'simple-Incline1'     => n2_('Incline 1'),
                'simple-Incline2'     => n2_('Incline 2'),
                'simple-Incline3'     => n2_('Incline 3'),
                'simple-InverseArrow' => n2_('Inverse arrow'),
                'simple-Rectangle'    => n2_('Rectangle'),
                'simple-Slopes'       => n2_('Slopes'),
                'simple-Tilt1'        => n2_('Tilt 1'),
                'simple-Tilt2'        => n2_('Tilt 2'),
                'simple-Triangle1'    => n2_('Triangle 1'),
                'simple-Triangle2'    => n2_('Triangle 2'),
                'simple-Wave1'        => n2_('Wave 1'),
                'simple-Wave2'        => n2_('Wave 2'),
                'simple-Waves'        => n2_('Waves'),
                'bicolor'             => array(
                    'label'   => n2_('2 Colors'),
                    'options' => array(
                        'bi-Fan'         => n2_('Fan'),
                        'bi-MaskedWaves' => n2_('Masked waves'),
                        'bi-Ribbon'      => n2_('Ribbon'),
                        'bi-Waves'       => n2_('Waves')
                    )
                )
            );
        }

        parent::__construct($insertAt, $name, $label, $default, $parameters);
    }

    protected function renderOptions($options) {

        $html = '<option value="0" ' . $this->isSelected('0') . '>' . n2_('Disabled') . '</option>';

        $html .= $this->renderOptionsRecursive(self::$_options);

        return $html;
    }

    private function renderOptionsRecursive($options) {
        $html = '';

        foreach ($options AS $value => $option) {
            if (is_array($option)) {
                $html .= Html::tag('optgroup', array('label' => $option['label']), $this->renderOptionsRecursive($option['options']));
            } else {
                $html .= '<option value="' . $value . '" ' . $this->isSelected($value) . '>' . $option . '</option>';

            }
        }

        return $html;
    }
}