<?php


namespace Nextend\SmartSlider3Pro\Widget\Html\HtmlCode;


use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3\Widget\AbstractWidget;

class HtmlCode extends AbstractWidget {

    protected $key = 'widget-html-';

    protected $defaults = array(
        'widget-html-position-mode' => 'simple',
        'widget-html-position-area' => 2,
        'widget-html-code'          => '',
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-html-row-1');

        new WidgetPosition($row1, 'widget-html-position', n2_('Position'));

        new Textarea($row1, 'widget-html-code', 'HTML', '', array(
            'height' => 300,
            'width'  => 600
        ));
    }
}