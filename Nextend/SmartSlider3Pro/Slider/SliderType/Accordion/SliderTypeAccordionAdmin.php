<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Accordion;


use Nextend\Framework\Form\Container\ContainerRowGroup;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Select\Easing;
use Nextend\Framework\Form\Element\Select\Skin;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\Framework\Form\Insert\InsertAfter;
use Nextend\Framework\Form\Insert\InsertBefore;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeAdmin;

class SliderTypeAccordionAdmin extends AbstractSliderTypeAdmin {

    protected $ordering = 5;

    public function isDepreciated() {
        return true;
    }

    public function getLabel() {
        return n2_x('Accordion', 'Slider type');
    }

    public function getLabelFull() {
        return n2_x('Accordion slider', 'Slider type');
    }

    public function getIcon() {
        return 'ssi_64 ssi_64--accordionslider';
    }

    public function prepareForm($form) {

        Notification::notice(sprintf('%s is deprecated and will be removed after %s.', 'Accordion type', 'December 31st, 2020'));

        $rowGroup = new ContainerRowGroup(new InsertAfter($form->getElement('/general/design/design-1')), 'slider-type-accordion-group', false);

        $rowSkin = new FieldsetRow($rowGroup, 'slider-type-accordion-skin');

        new Radio($rowSkin, 'orientation', n2_('Orientation'), 'horizontal', array(
            'options' => array(
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));

        new Skin($rowSkin, 'slider-preset', n2_('Preset'), '', array(
            'fixed'   => true,
            'options' => array(
                'dark'  => array(
                    'label'    => n2_('Dark'),
                    'settings' => array(
                        'tab-normal-color'   => '3E3E3E',
                        'outer-border'       => 6,
                        'outer-border-color' => '3E3E3Eff',
                        'inner-border'       => 6,
                        'inner-border-color' => '222222ff',
                        'title-font'         => '{"data":[{"extra":"text-transform: uppercase;","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{"extra":""}]}',
                        'slider-outer-css'   => '',
                        'slider-inner-css'   => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;',
                        'slider-title-css'   => 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);'
                    )
                ),
                'light' => array(
                    'label'    => n2_('Light'),
                    'settings' => array(
                        'tab-normal-color'   => 'e9e8e0',
                        'outer-border'       => 6,
                        'outer-border-color' => 'e9e8e0',
                        'inner-border'       => 6,
                        'inner-border-color' => 'd2d0c8ff',
                        'title-font'         => '{"data":[{"extra":"text-transform: uppercase;","color":"4d4d4dff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{"extra":"","color":"ffffffff"}]}',
                        'slider-outer-css'   => 'box-shadow: 0 0 0 1px #cccccc inset;',
                        'slider-inner-css'   => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .2) inset;',
                        'slider-title-css'   => 'box-shadow: 0 0 2px 1px RGBA(0, 0, 0, .2);'
                    )
                )
            )
        ));

        $rowSettings = new FieldsetRow($rowGroup, 'slider-type-accordion-settings');

        new NumberAutoComplete($rowSettings, 'outer-border', n2_('Border outer width'), 6, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                6
            )
        ));
        new Color($rowSettings, 'outer-border-color', n2_('Outer color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new NumberAutoComplete($rowSettings, 'inner-border', n2_('Border inner width'), 6, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                6
            )
        ));
        new Color($rowSettings, 'inner-border-color', n2_('Inner color'), '222222ff', array(
            'alpha' => true
        ));
        new Number($rowSettings, 'border-radius', n2_('Border radius'), 6, array(
            'unit' => 'px',
            'wide' => 3
        ));

        $rowTab = new FieldsetRow($rowGroup, 'slider-type-accordion-title');

        new Color($rowTab, 'tab-normal-color', n2_('Tab background'), '3E3E3E');
        new Color($rowTab, 'tab-active-color', n2_('Tab background - Active'), '87B801');

        new NumberAutoComplete($rowTab, 'slide-margin', n2_('Slide margin'), 2, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                0,
                2
            )
        ));

        new NumberAutoComplete($rowTab, 'title-size', n2_('Title size'), 30, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                30
            )
        ));

        new NumberAutoComplete($rowTab, 'title-margin', n2_('Title margin'), 10, array(
            'unit'   => 'px',
            'style'  => 'width:20px;',
            'values' => array(
                10
            )
        ));

        new NumberAutoComplete($rowTab, 'title-border-radius', n2_('Border radius'), 2, array(
            'unit'   => 'px',
            'style'  => 'width:15px;',
            'values' => array(
                0,
                2
            )
        ));

        new Font($rowTab, 'title-font', n2_('Font'), '{"data":[{"extra":"text-transform: uppercase;","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{"extra":""}]}', array(
            'mode' => 'accordionslidetitle'
        ));

        $rowCSS = new FieldsetRow($rowGroup, 'slider-type-accordion-css');

        new Textarea($rowCSS, 'slider-outer-css', n2_('Outer') . ' CSS', '', array(
            'height' => 26,
            'resize' => 'both'
        ));

        new Textarea($rowCSS, 'slider-inner-css', n2_('Inner') . ' CSS', 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;', array(
            'height' => 26,
            'resize' => 'both'
        ));

        new Textarea($rowCSS, 'slider-title-css', n2_('Title') . ' CSS', 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);', array(
            'height' => 26,
            'resize' => 'both'
        ));


        $tableAnimation = new ContainerTable(new InsertBefore($form->getElement('/animations/effects')), 'slider-type-accordion-animation', n2_('Animation'));

        $rowAnimation = new FieldsetRow($tableAnimation, 'slider-type-accordion-animation-1');

        new NumberAutoComplete($rowAnimation, 'animation-duration', n2_('Animation duration'), 1000, array(
            'unit'   => 'ms',
            'style'  => 'width:30px;',
            'values' => array(
                800,
                1000,
                1500,
                2000
            )
        ));

        new Easing($rowAnimation, 'animation-easing', n2_('Easing'), 'easeOutQuad');

        new OnOff($rowAnimation, 'carousel', n2_x('Carousel', 'Feature'), 1, array(
            'tipLabel'        => n2_x('Carousel', 'Feature'),
            'tipDescription'  => n2_('If you turn off this option, you can\'t switch to the first slide from the last one.'),
            'relatedFieldsOn' => array(
                'slidercontrolsBlockCarouselInteraction'
            )
        ));

        /**
         * Removing slider settings which are unnecessary for Accordion slider type.
         */
        $form->getElement('/animations/effects')
             ->remove();
        $form->getElement('/controls/widget-bar')
             ->remove();
        $form->getElement('/slides/slides-parallax')
             ->remove();
        $form->getElement('/controls/widget-fullscreen')
             ->remove();
        $form->getElement('/size/size/size-2')
             ->remove();

        $form->getElement('/size/responsive-mode/responsive-mode-row-1/responsive-mode')
             ->removeOption('fullpage');
    }
}