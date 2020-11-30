<?php


namespace Nextend\SmartSlider3Pro\Widget\Bullet\BulletNumbers;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3\Widget\Bullet\AbstractBullet;

class BulletNumbers extends AbstractBullet {

    protected $defaults = array(
        'widget-bullet-position-mode'        => 'simple',
        'widget-bullet-position-area'        => 10,
        'widget-bullet-position-offset'      => 5,
        'widget-bullet-action'               => 'click',
        'widget-bullet-style'                => '{"data":[{"backgroundcolor":"000000ab","padding":"5|*|9|*|5|*|9|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":"min-width: 10px;\nmargin: 4px;"},{"backgroundcolor":"5F39C2FF","padding":"5|*|9|*|5|*|9|*|px"}]}',
        'widget-bullet-font'                 => '{"data":[{"color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.2","bold":0,"italic":0,"underline":0,"align":"center","extra":""},{"color":"ffffffff"}]}',
        'widget-bullet-bar'                  => '',
        'widget-bullet-align'                => 'center',
        'widget-bullet-orientation'          => 'auto',
        'widget-bullet-bar-full-size'        => 0,
        'widget-bullet-overlay'              => 0,
        'widget-bullet-thumbnail-show-image' => 0,
        'widget-bullet-thumbnail-width'      => 60,
        'widget-bullet-thumbnail-style'      => '{"data":[{"backgroundcolor":"00000080","padding":"3|*|3|*|3|*|3|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"3","extra":"margin: 5px;"}]}',
        'widget-bullet-thumbnail-side'       => 'before'
    );


    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-bullet-number-row-1');

        new WidgetPosition($row1, 'widget-bullet-position', n2_('Position'));

        new Select($row1, 'widget-bullet-action', n2_('Action'), '', array(
            'options' => array(
                'click'      => n2_('Click'),
                'mouseenter' => n2_('Hover')
            )
        ));

        $row2 = new FieldsetRow($container, 'widget-bullet-number-row-2');

        new Style($row2, 'widget-bullet-style', n2_('Dot'), '', array(
            'mode'    => 'dot',
            'font'    => 'sliderwidget-bullet-font',
            'style2'  => 'sliderwidget-bullet-bar',
            'preview' => 'SmartSliderAdminWidgetBulletNumbers'
        ));

        new Font($row2, 'widget-bullet-font', n2_('Text'), '', array(
            'mode'    => 'dot',
            'style'   => 'sliderwidget-bullet-style',
            'style2'  => 'sliderwidget-bullet-bar',
            'preview' => 'SmartSliderAdminWidgetBulletNumbers'
        ));

        new Style($row2, 'widget-bullet-bar', n2_('Bar'), '', array(
            'mode'    => 'simple',
            'font'    => 'sliderwidget-bullet-font',
            'style2'  => 'sliderwidget-bullet-style',
            'preview' => 'SmartSliderAdminWidgetBulletNumbers'
        ));

        new OnOff($row2, 'widget-bullet-bar-full-size', n2_('Bar full size'), '');
        new Select($row2, 'widget-bullet-align', n2_('Align'), '', array(
            'options' => array(
                'left'   => n2_('Left'),
                'center' => n2_('Center'),
                'right'  => n2_('Right')
            )
        ));
        new Select($row2, 'widget-bullet-orientation', n2_('Orientation'), '', array(
            'options' => array(
                'auto'       => n2_('Auto'),
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));
        new OnOff($row2, 'widget-bullet-overlay', n2_('Overlay'), '');
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get($this->key . 'style'));
        $export->addVisual($params->get($this->key . 'bar'));
        $export->addVisual($params->get($this->key . 'font'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'style', $import->fixSection($params->get($this->key . 'style')));
        $params->set($this->key . 'bar', $import->fixSection($params->get($this->key . 'bar')));
        $params->set($this->key . 'font', $import->fixSection($params->get($this->key . 'font')));
    }
}