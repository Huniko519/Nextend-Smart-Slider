<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowReveal;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio\ImageListFromFolder;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Widget\Arrow\AbstractWidgetArrow;

class ArrowReveal extends AbstractWidgetArrow {

    protected $defaults = array(
        'widget-arrow-previous-position-mode'   => 'simple',
        'widget-arrow-previous-position-area'   => 6,
        'widget-arrow-previous-position-offset' => 0,
        'widget-arrow-next-position-mode'       => 'simple',
        'widget-arrow-next-position-area'       => 7,
        'widget-arrow-next-position-offset'     => 0,
        'widget-arrow-font'                     => '',
        'widget-arrow-background'               => '00000080',
        'widget-arrow-title-show'               => 0,
        'widget-arrow-title-font'               => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","extra":""},{}]}',
        'widget-arrow-title-background'         => '000000cc',
        'widget-arrow-animation'                => 'slide',
        'widget-arrow-previous-color'           => 'ffffffcc',
        'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/reveal/reveal/previous/simple-horizontal.svg',
        'widget-arrow-mirror'                   => 1,
        'widget-arrow-next-color'               => 'ffffffcc',
        'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/reveal/reveal/next/simple-horizontal.svg'
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-arrow-reveal-row-1');

        new Color($row1, 'widget-arrow-background', n2_('Background'), '', array(
            'alpha' => true
        ));

        new Select($row1, 'widget-arrow-animation', n2_('Animation'), '', array(
            'options' => array(
                'slide' => n2_x('Slide', 'Animation'),
                'fade'  => n2_('Fade'),
                'turn'  => n2_('Turn')
            )
        ));

        $rowPrevious = new FieldsetRow($container, 'widget-arrow-reveal-row-previous');

        new ImageListFromFolder($rowPrevious, 'widget-arrow-previous', n2_x('Previous', 'Arrow direction'), '', array(
            'post'   => 'break',
            'folder' => self::getAssetsPath() . '/previous/'
        ));
        new Color($rowPrevious, 'widget-arrow-previous-color', n2_('Color'), '', array(
            'alpha' => true
        ));

        new OnOff($rowPrevious, 'widget-arrow-mirror', n2_('Mirror'), '', array(
            'relatedFieldsOff' => array(
                'sliderwidget-arrow-next',
                'sliderwidget-arrow-next-color'
            )
        ));

        new ImageListFromFolder($rowPrevious, 'widget-arrow-next', n2_x('Next', 'Arrow direction'), '', array(
            'post'   => 'break',
            'folder' => self::getAssetsPath() . '/next/'
        ));
        new Color($rowPrevious, 'widget-arrow-next-color', n2_('Color'), '', array(
            'alpha' => true
        ));

        $rowTitle = new FieldsetRow($container, 'widget-arrow-reveal-row-title');
        new OnOff($rowTitle, 'widget-arrow-title-show', n2_('Slide title'), 0, array(
            'relatedFieldsOn' => array(
                'sliderwidget-arrow-title-font',
                'sliderwidget-arrow-title-background'
            )
        ));
        new Font($rowTitle, 'widget-arrow-title-font', n2_('Font'), '', array(
            'mode'    => 'link',
            'preview' => 'SmartSliderAdminWidgetArrowReveal'
        ));
        new Color($rowTitle, 'widget-arrow-title-background', n2_('Background color'), '', array(
            'alpha' => true
        ));
    }

    public function prepareExport($export, $params) {
        $export->addVisual($params->get($this->key . 'title-font'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'title-font', $import->fixSection($params->get($this->key . 'title-font', '')));
    }
}