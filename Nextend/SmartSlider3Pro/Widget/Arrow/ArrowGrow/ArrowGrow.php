<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowGrow;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio\ImageListFromFolder;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Widget\Arrow\AbstractWidgetArrow;

class ArrowGrow extends AbstractWidgetArrow {

    protected $defaults = array(
        'widget-arrow-previous-position-mode'   => 'simple',
        'widget-arrow-previous-position-area'   => 6,
        'widget-arrow-previous-position-offset' => 15,
        'widget-arrow-next-position-mode'       => 'simple',
        'widget-arrow-next-position-area'       => 7,
        'widget-arrow-next-position-offset'     => 15,
        'widget-arrow-style'                    => '{"data":[{"backgroundcolor":"00000080","padding":"3|*|3|*|3|*|3|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"50","extra":""},{"backgroundcolor":"1D81F9FF"}]}',
        'widget-arrow-font'                     => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","extra":""},{}]}',
        'widget-arrow-animation-delay'          => 0,
        'widget-arrow-previous-color'           => 'ffffffcc',
        'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/grow/grow/previous/simple-horizontal.svg',
        'widget-arrow-mirror'                   => 1,
        'widget-arrow-next-color'               => 'ffffffcc',
        'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/grow/grow/next/simple-horizontal.svg'
    );

    public function renderFields($container) {

        $rowIcon = new FieldsetRow($container, 'widget-arrow-grow-row-icon');
        new ImageListFromFolder($rowIcon, 'widget-arrow-previous', n2_x('Previous', 'Arrow direction'), '', array(
            'folder' => self::getAssetsPath() . '/previous/'
        ));
        new Color($rowIcon, 'widget-arrow-previous-color', n2_('Color'), '', array(
            'alpha' => true
        ));

        new OnOff($rowIcon, 'widget-arrow-mirror', n2_('Mirror'), '', array(
            'relatedFieldsOff' => array(
                'sliderwidget-arrow-next',
                'sliderwidget-arrow-next-color'
            )
        ));

        new ImageListFromFolder($rowIcon, 'widget-arrow-next', n2_x('Next', 'Arrow direction'), '', array(
            'folder' => self::getAssetsPath() . '/next/'
        ));
        new Color($rowIcon, 'widget-arrow-next-color', n2_('Color'), '', array(
            'alpha' => true
        ));

        $row3 = new FieldsetRow($container, 'widget-arrow-grow-row-3');

        new Style($row3, 'widget-arrow-style', n2_('Arrow'), '', array(
            'mode'    => 'button',
            'preview' => 'SmartSliderAdminWidgetArrowGrow'
        ));
        new Font($row3, 'widget-arrow-font', n2_('Text'), '', array(
            'mode'    => 'link',
            'preview' => 'SmartSliderAdminWidgetArrowGrow',
            'style'   => 'sliderwidget-arrow-style'
        ));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get($this->key . 'style'));
        $export->addVisual($params->get($this->key . 'font'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'style', $import->fixSection($params->get($this->key . 'style', '')));
        $params->set($this->key . 'font', $import->fixSection($params->get($this->key . 'font', '')));
    }
}