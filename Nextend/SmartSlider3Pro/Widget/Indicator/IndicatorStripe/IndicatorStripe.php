<?php


namespace Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorStripe;


use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3Pro\Widget\Indicator\AbstractWidgetIndicator;

class IndicatorStripe extends AbstractWidgetIndicator {

    protected $defaults = array(
        'widget-indicator-position-mode' => 'simple',
        'widget-indicator-position-area' => 9,
        'widget-indicator-width'         => '100%',
        'widget-indicator-height'        => 6,
        'widget-indicator-track'         => '000000ab',
        'widget-indicator-bar'           => '1D81F9FF'
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-indicator-stripe-1');

        new WidgetPosition($row1, 'widget-indicator-position', n2_('Position'));

        new Text($row1, 'widget-indicator-width', n2_('Width'), '', array(
            'style' => 'width:30px;',
            'unit'  => 'px'
        ));
        new Number($row1, 'widget-indicator-height', n2_('Height'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));


        new Color($row1, 'widget-indicator-track', n2_('Track color'), '', array(
            'alpha' => true
        ));
        new Color($row1, 'widget-indicator-bar', n2_('Bar color'), '', array(
            'alpha' => true
        ));
    }

}