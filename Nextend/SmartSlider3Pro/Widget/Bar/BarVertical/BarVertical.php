<?php


namespace Nextend\SmartSlider3Pro\Widget\Bar\BarVertical;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3\Widget\Bar\AbstractWidgetBar;

class BarVertical extends AbstractWidgetBar {

    protected $defaults = array(
        'widget-bar-position-mode'    => 'simple',
        'widget-bar-position-area'    => 6,
        'widget-bar-position-offset'  => 0,
        'widget-bar-style'            => '{"data":[{"backgroundcolor":"000000ab","padding":"20|*|20|*|20|*|20|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""}]}',
        'widget-bar-font-title'       => '{"data":[{"color":"ffffffff","size":"16||px","tshadow":"0|*|0|*|0|*|000000c7","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
        'widget-bar-font-description' => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000c7","afont":"Montserrat","lineheight":"1.6","bold":0,"italic":0,"underline":0,"align":"left","extra":"margin-top:10px;"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
        'widget-bar-width'            => '200px',
        'widget-bar-height'           => '100%',
        'widget-bar-animate'          => 0
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-bar-vertical-row-1');

        new WidgetPosition($row1, 'widget-bar-position', n2_('Position'));

        new OnOff($row1, 'widget-bar-animate', n2_('Animate'));

        new Style($row1, 'widget-bar-style', n2_('Bar'), '', array(
            'mode'    => 'simple',
            'font'    => 'sliderwidget-bar-font-title',
            'font2'   => 'sliderwidget-bar-font-description',
            'preview' => 'SmartSliderAdminWidgetBarVertical'
        ));

        new Font($row1, 'widget-bar-font-title', n2_('Title'), '', array(
            'mode'    => 'simple',
            'style'   => 'sliderwidget-bar-style',
            'preview' => 'SmartSliderAdminWidgetBarVertical'
        ));

        new Font($row1, 'widget-bar-font-description', n2_('Description'), '', array(
            'mode'    => 'simple',
            'style'   => 'sliderwidget-bar-style',
            'preview' => 'SmartSliderAdminWidgetBarVertical'
        ));

        $row2 = new FieldsetRow($container, 'widget-bar-vertical-row-2');

        new Text($row2, 'widget-bar-width', n2_('Width'));
        new Text($row2, 'widget-bar-height', n2_('Height'), '');
    }


    public function prepareExport($export, $params) {
        $export->addVisual($params->get($this->key . 'style'));
        $export->addVisual($params->get($this->key . 'font-title'));
        $export->addVisual($params->get($this->key . 'font-description'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'style', $import->fixSection($params->get($this->key . 'style', '')));
        $params->set($this->key . 'font-title', $import->fixSection($params->get($this->key . 'font-title', '')));
        $params->set($this->key . 'font-description', $import->fixSection($params->get($this->key . 'font-description', '')));
    }
}