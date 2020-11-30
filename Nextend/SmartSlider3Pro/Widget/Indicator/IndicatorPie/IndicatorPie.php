<?php


namespace Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorPie;

use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3Pro\Widget\Indicator\AbstractWidgetIndicator;

class IndicatorPie extends AbstractWidgetIndicator {

    protected $defaults = array(
        'widget-indicator-position-mode'   => 'simple',
        'widget-indicator-position-area'   => 4,
        'widget-indicator-position-offset' => 15,
        'widget-indicator-size'            => 25,
        'widget-indicator-thickness'       => 30,
        'widget-indicator-track'           => '000000ab',
        'widget-indicator-bar'             => 'ffffffff'
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-indicator-pie-1');

        new WidgetPosition($row1, 'widget-indicator-position', n2_('Position'));

        new Number($row1, 'widget-indicator-size', n2_('Size'), '', array(
            'wide' => 4,
            'unit' => 'px'
        ));
        new Number($row1, 'widget-indicator-thickness', n2_('Line thickness'), '', array(
            'wide' => 3,
            'unit' => '%'
        ));

        new Color($row1, 'widget-indicator-track', n2_('Track color'), '', array(
            'alpha' => true
        ));
        new Color($row1, 'widget-indicator-bar', n2_('Bar color'), '', array(
            'alpha' => true
        ));

        new Style($row1, 'widget-indicator-style', n2_('Style'), '', array(
            'mode'    => 'button',
            'preview' => 'SmartSliderAdminWidgetIndicatorPie'
        ));
    }
}