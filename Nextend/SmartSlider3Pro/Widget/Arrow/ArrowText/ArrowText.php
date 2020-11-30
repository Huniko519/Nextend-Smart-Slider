<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowText;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3\Widget\Arrow\AbstractWidgetArrow;

class ArrowText extends AbstractWidgetArrow {

    protected $key = 'widget-arrow-';

    protected $defaults = array(
        'widget-arrow-style'                    => '{"data":[{"backgroundcolor":"000000ab","padding":"8|*|10|*|8|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"3","extra":""},{"backgroundcolor":"04C018FF"}]}',
        'widget-arrow-font'                     => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","extra":""},{}]}',
        'widget-arrow-previous-position-mode'   => 'simple',
        'widget-arrow-previous-position-area'   => 6,
        'widget-arrow-previous-position-offset' => 15,
        'widget-arrow-next-position-mode'       => 'simple',
        'widget-arrow-next-position-area'       => 7,
        'widget-arrow-next-position-offset'     => 15
    );

    public function __construct($widgetGroup, $name, $defaults = array()) {
        parent::__construct($widgetGroup, $name, array_merge(array(
            'widget-arrow-previous-label' => n2_('Prev'),
            'widget-arrow-next-label'     => n2_('Next')
        ), $defaults));
    }

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-arrow-text-row-1');

        new Text($row1, 'widget-arrow-previous-label', n2_('Previous label'));
        new Text($row1, 'widget-arrow-next-label', n2_('Next label'));

        $row2 = new FieldsetRow($container, 'widget-arrow-text-row-2');

        new Style($row2, 'widget-arrow-style', n2_('Arrow'), '', array(
            'mode'    => 'button',
            'font'    => 'sliderwidget-arrow-font',
            'preview' => 'SmartSliderAdminWidgetArrowText'
        ));

        new Font($row2, 'widget-arrow-font', n2_('Text'), '', array(
            'mode'    => 'link',
            'style'   => 'sliderwidget-arrow-style',
            'preview' => 'SmartSliderAdminWidgetArrowText'
        ));

        new WidgetPosition($row2, 'widget-arrow-previous-position', n2_('Previous position'));

        new WidgetPosition($row2, 'widget-arrow-next-position', n2_('Next position'));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get($this->key . 'font'));
        $export->addVisual($params->get($this->key . 'style'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'font', $import->fixSection($params->get($this->key . 'font', '')));
        $params->set($this->key . 'style', $import->fixSection($params->get($this->key . 'style', '')));
    }
}