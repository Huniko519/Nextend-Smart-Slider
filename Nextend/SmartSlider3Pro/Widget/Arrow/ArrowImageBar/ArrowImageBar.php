<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowImageBar;


use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio\ImageListFromFolder;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Widget\Arrow\AbstractWidgetArrow;

class ArrowImageBar extends AbstractWidgetArrow {

    protected $defaults = array(
        'widget-arrow-previous-position-mode'   => 'simple',
        'widget-arrow-previous-position-area'   => 2,
        'widget-arrow-previous-position-offset' => 0,
        'widget-arrow-next-position-mode'       => 'simple',
        'widget-arrow-next-position-area'       => 4,
        'widget-arrow-next-position-offset'     => 0,
        'widget-arrow-width'                    => 100,
        'widget-arrow-previous-color'           => 'ffffffcc',
        'widget-arrow-previous'                 => '$ss$/plugins/widgetarrow/imagebar/imagebar/previous/simple-horizontal.svg',
        'widget-arrow-mirror'                   => 1,
        'widget-arrow-next-color'               => 'ffffffcc',
        'widget-arrow-next'                     => '$ss$/plugins/widgetarrow/imagebar/imagebar/next/simple-horizontal.svg'
    );

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-arrow-image-bar-row-1');
        new Number($row1, 'widget-arrow-width', n2_('Width'), 0, array(
            'style' => 'width:40px;',
            'unit'  => 'px'
        ));

        $rowIcon = new FieldsetRow($container, 'widget-arrow-image-bar-row-icon');
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
    }
}