<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Showcase;


use Nextend\Framework\Form\Container\ContainerRowGroup;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Select\Easing;
use Nextend\Framework\Form\Element\Select\Skin;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\TextAutoComplete;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\Framework\Form\Insert\InsertAfter;
use Nextend\Framework\Form\Insert\InsertBefore;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeAdmin;

class SliderTypeShowcaseAdmin extends AbstractSliderTypeAdmin {

    protected $ordering = 4;

    public function getLabel() {
        return n2_('Showcase');
    }

    public function getLabelFull() {
        return n2_x('Showcase slider', 'Slider type');
    }

    public function getIcon() {
        return 'ssi_64 ssi_64--showcase';
    }

    public function prepareForm($form) {

        $tableSlideSize = new ContainerTable(new InsertAfter($form->getElement('/size/size')), 'slider-type-showcase-settings-size', n2_('Slide size'));

        $rowSettingsSlide = new FieldsetRow($tableSlideSize, 'slider-type-showcase-settings-slide-size');

        new NumberAutoComplete($rowSettingsSlide, 'slide-width', n2_('Slide width'), 600, array(
            'values' => array(
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
        new NumberAutoComplete($rowSettingsSlide, 'slide-height', n2_('Slide height'), 400, array(
            'values' => array(
                300,
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'wide'   => 5,
            'post'   => 'break'
        ));

        new NumberAutoComplete($rowSettingsSlide, 'slide-distance', n2_('Slide distance'), 60, array(
            'values'         => array(
                0,
                60,
                150
            ),
            'unit'           => 'px',
            'wide'           => 3,
            'tipLabel'       => n2_('Slide distance'),
            'tipDescription' => n2_('Fix space between the slides.')
        ));

        $rowSettingsSliderBackground = new FieldsetRow(new InsertBefore($form->getElement('/general/design/design-1')), 'slider-type-showcase-settings-slider-background');

        new FieldImage($rowSettingsSliderBackground, 'background', n2_('Slider background image'), '', array(
            'width'         => '200',
            'relatedFields' => array(
                'sliderbackground-fixed',
                'sliderbackground-size'
            )
        ));
        new OnOff($rowSettingsSliderBackground, 'background-fixed', n2_('Fixed'), 0);
        new TextAutoComplete($rowSettingsSliderBackground, 'background-size', n2_('Size'), 'cover', array(
            'values' => array(
                'cover',
                'contain',
                'auto'
            )
        ));

        new Color($rowSettingsSliderBackground, 'background-color', n2_('Slider background color'), 'FFFFFF00', array(
            'alpha' => true,
            'post'  => 'break'
        ));

        $rowGroupSlides = new ContainerRowGroup(new InsertAfter($form->getElement('/slides/slides-design/slides-design-1')), 'slider-type-showcase-group-slides', false);

        $rowSettingsSlideDisplay = new FieldsetRow($rowGroupSlides, 'slider-type-showcase-settings-slide-display');

        new Number($rowSettingsSlideDisplay, 'slide-border-width', n2_('Slide border width'), 0, array(
            'unit'          => 'px',
            'wide'          => 3,
            'relatedFields' => array('sliderslide-border-color')
        ));
        new Color($rowSettingsSlideDisplay, 'slide-border-color', n2_('Slide border color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new Number($rowSettingsSlideDisplay, 'slide-border-radius', n2_('Slide border radius'), 0, array(
            'unit' => 'px',
            'wide' => 3,
            'post' => 'break'
        ));

        $rowGroupGeneral = new ContainerRowGroup(new InsertAfter($form->getElement('/general/design/design-1')), 'slider-type-showcase-group-general', false);

        $rowSettingsSlider = new FieldsetRow($rowGroupGeneral, 'slider-type-showcase-settings-slider');

        new Number($rowSettingsSlider, 'border-width', n2_('Slider border width'), 0, array(
            'unit'          => 'px',
            'wide'          => 3,
            'relatedFields' => array('sliderborder-color')
        ));
        new Color($rowSettingsSlider, 'border-color', n2_('Slider border color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new Number($rowSettingsSlider, 'border-radius', n2_('Slider border radius'), 0, array(
            'unit' => 'px',
            'wide' => 3,
            'post' => 'break'
        ));

        $rowSlideCSS = new FieldsetRow($rowGroupSlides, 'slider-type-showcase-settings-slidecss');

        new Skin($rowSlideCSS, 'slide-preset', n2_('Slide CSS Preset'), '', array(
            'post'    => 'break',
            'options' => array(
                'shadow'       => array(
                    'label'    => n2_('Light shadow'),
                    'settings' => array(
                        'slide-css' => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);'
                    )
                ),
                'borderradius' => array(
                    'label'    => n2_('Border radius'),
                    'settings' => array(
                        'slide-css' => "box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);"
                    )
                )
            )
        ));

        new Textarea($rowSlideCSS, 'slide-css', 'Slide CSS', '', array(
            'height' => 26,
            'resize' => 'both'
        ));


        $rowSliderCSS = new FieldsetRow($rowGroupGeneral, 'slider-type-showcase-settings-slidercss');

        new Skin($rowSliderCSS, 'slider-preset', n2_('Slider CSS Preset'), '', array(
            'post'    => 'break',
            'options' => array(
                'shadow'       => array(
                    'label'    => n2_('Light shadow'),
                    'settings' => array(
                        'slider-css' => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);'
                    )
                ),
                'shadow2'      => array(
                    'label'    => n2_('Dark shadow'),
                    'settings' => array(
                        'slider-css' => 'box-shadow: 0 2px 4px 1px rgba(0, 0, 0, 0.6);'
                    )
                ),
                'photo'        => array(
                    'label'    => n2_('Photo'),
                    'settings' => array(
                        'slider-css'   => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);',
                        'border-width' => '8',
                        'border-color' => 'FFFFFFFF'
                    )
                ),
                'roundedphoto' => array(
                    'label'    => n2_('Photo rounded'),
                    'settings' => array(
                        'slider-css'    => 'box-shadow: 1px 0 5px RGBA(0, 0, 0, 0.2), -1px 0 5px RGBA(0, 0, 0, 0.2);',
                        'border-width'  => '5',
                        'border-color'  => 'FFFFFFFF',
                        'border-radius' => '12'
                    )
                )
            )
        ));

        new Textarea($rowSliderCSS, 'slider-css', 'Slider CSS', '', array(
            'height' => 26,
            'resize' => 'both'
        ));


        $tableShowcaseAnimation = new ContainerTable(new InsertBefore($form->getElement('/animations/effects')), 'slider-type-showcase-animation', n2_('Showcase animation'));

        $rowAnimation1 = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-animation-1');

        new Skin($rowAnimation1, 'animation-preset', n2_('Preset'), '', array(
            'fixed'   => true,
            'options' => array(
                'none'                => array(
                    'label'    => n2_('Default'),
                    'settings' => array(
                        'slide-distance' => 60,
                        'perspective'    => 1000,
                        'opacity'        => '0|*|100|*|100|*|100',
                        'scale'          => '0|*|100|*|100|*|100',
                        'translate-x'    => '0|*|0|*|0|*|0',
                        'translate-y'    => '0|*|0|*|0|*|0',
                        'translate-z'    => '0|*|0|*|0|*|0',
                        'rotate-x'       => '0|*|0|*|0|*|0',
                        'rotate-y'       => '0|*|0|*|0|*|0',
                        'rotate-z'       => '0|*|0|*|0|*|0'
                    )
                ),
                'horizontal'          => array(
                    'label'    => n2_('Horizontal showcase'),
                    'settings' => array(
                        'animation-direction' => 'horizontal',
                        'slide-distance'      => 60,
                        'perspective'         => 1000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '0|*|100|*|100|*|100',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'vertical'            => array(
                    'label'    => n2_('Vertical showcase'),
                    'settings' => array(
                        'animation-direction' => 'vertical',
                        'slide-distance'      => 60,
                        'perspective'         => 1000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '0|*|100|*|100|*|100',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'horizontalcoverflow' => array(
                    'label'    => n2_('Horizontal cover flow'),
                    'settings' => array(
                        'animation-direction' => 'horizontal',
                        'slide-distance'      => 10,
                        'perspective'         => 2000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '1|*|70|*|100|*|70',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '0|*|0|*|0|*|0',
                        'rotate-y'            => '1|*|45|*|0|*|-45',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                ),
                'verticalcoverflow'   => array(
                    'label'    => n2_('Vertical cover flow'),
                    'settings' => array(
                        'animation-direction' => 'vertical',
                        'slide-distance'      => 10,
                        'perspective'         => 2000,
                        'opacity'             => '0|*|100|*|100|*|100',
                        'scale'               => '1|*|70|*|100|*|70',
                        'translate-x'         => '0|*|0|*|0|*|0',
                        'translate-y'         => '0|*|0|*|0|*|0',
                        'translate-z'         => '0|*|0|*|0|*|0',
                        'rotate-x'            => '1|*|-45|*|0|*|45',
                        'rotate-y'            => '0|*|0|*|0|*|0',
                        'rotate-z'            => '0|*|0|*|0|*|0'
                    )
                )
            )
        ));


        $rowAnimation2 = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-animation-2');

        new NumberAutoComplete($rowAnimation2, 'animation-duration', n2_('Duration'), 800, array(
            'wide'   => 5,
            'min'    => 200,
            'values' => array(
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms'
        ));

        new Easing($rowAnimation2, 'animation-easing', n2_('Easing'), 'easeOutQuad');

        new NumberAutoComplete($rowAnimation2, 'perspective', n2_('Perspective'), 1000, array(
            'min'    => 0,
            'values' => array(
                0,
                1000
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));

        new Radio($rowAnimation2, 'animation-direction', n2_('Direction'), 'horizontal', array(
            'options' => array(
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));

        $rowAnimationTransform = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-animation-opacity');

        $opacity = new Mixed($rowAnimationTransform, 'opacity', false, '0|*|100|*|100|*|100');
        new OnOff($opacity, 'opacity-1', n2_('Opacity'), 0, array(
            'relatedFieldsOn' => array(
                'opacityslideropacity-2',
                'opacityslideropacity-3',
                'opacityslideropacity-4'
            )
        ));
        new NumberAutoComplete($opacity, 'opacity-2', n2_('Before'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));
        new NumberAutoComplete($opacity, 'opacity-3', n2_('Active'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));
        new NumberAutoComplete($opacity, 'opacity-4', n2_('After'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'max'    => 100,
            'values' => array(
                0,
                70,
                100
            ),
            'unit'   => '%'
        ));

        $scale = new Mixed($rowAnimationTransform, 'scale', false, '0|*|100|*|100|*|100');
        new OnOff($scale, 'scale-1', n2_('Scale'), 0, array(
            'relatedFieldsOn' => array(
                'scalesliderscale-2',
                'scalesliderscale-3',
                'scalesliderscale-4'
            )
        ));
        new NumberAutoComplete($scale, 'scale-2', n2_('Before'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));
        new NumberAutoComplete($scale, 'scale-3', n2_('Active'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));
        new NumberAutoComplete($scale, 'scale-4', n2_('After'), '', array(
            'wide'   => 3,
            'min'    => 0,
            'values' => array(
                0,
                50,
                80,
                90,
                100
            ),
            'unit'   => '%'
        ));


        $rowAnimationPosition = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-animation-x');

        $translateX = new Mixed($rowAnimationPosition, 'translate-x', false, '0|*|0|*|0|*|0');
        new OnOff($translateX, 'translate-x-1', 'X', 0, array(
            'relatedFieldsOn' => array(
                'translate-xslidertranslate-x-2',
                'translate-xslidertranslate-x-3',
                'translate-xslidertranslate-x-4'
            )
        ));
        new NumberAutoComplete($translateX, 'translate-x-2', n2_('Before'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateX, 'translate-x-3', n2_('Active'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateX, 'translate-x-4', n2_('After'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));

        $translateY = new Mixed($rowAnimationPosition, 'translate-y', false, '0|*|0|*|0|*|0');
        new OnOff($translateY, 'translate-y-1', 'Y', 0, array(
            'relatedFieldsOn' => array(
                'translate-yslidertranslate-y-2',
                'translate-yslidertranslate-y-3',
                'translate-yslidertranslate-y-4'
            )
        ));
        new NumberAutoComplete($translateY, 'translate-y-2', n2_('Before'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateY, 'translate-y-3', n2_('Active'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateY, 'translate-y-4', n2_('After'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));

        $translateZ = new Mixed($rowAnimationPosition, 'translate-z', false, '0|*|0|*|0|*|0');
        new OnOff($translateZ, 'translate-z-1', 'Z', 0, array(
            'relatedFieldsOn' => array(
                'translate-zslidertranslate-z-2',
                'translate-zslidertranslate-z-3',
                'translate-zslidertranslate-z-4'
            )
        ));
        new NumberAutoComplete($translateZ, 'translate-z-2', n2_('Before'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateZ, 'translate-z-3', n2_('Active'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($translateZ, 'translate-z-4', n2_('After'), '', array(
            'wide'   => 4,
            'values' => array(
                -100,
                0,
                100
            ),
            'unit'   => 'px'
        ));


        $rowAnimationRotate = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-animation-rotate-x');

        $rotateX = new Mixed($rowAnimationRotate, 'rotate-x', false, '0|*|0|*|0|*|0');
        new OnOff($rotateX, 'rotate-x-1', n2_('Rotate') . ' X', 0, array(
            'relatedFieldsOn' => array(
                'rotate-xsliderrotate-x-2',
                'rotate-xsliderrotate-x-3',
                'rotate-xsliderrotate-x-4'
            )
        ));
        new NumberAutoComplete($rotateX, 'rotate-x-2', n2_('Before'), '', array(
            'wide'   => 4,
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateX, 'rotate-x-3', n2_('Active'), '', array(
            'wide'   => 4,
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateX, 'rotate-x-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));

        $rotateY = new Mixed($rowAnimationRotate, 'rotate-y', false, '0|*|0|*|0|*|0');
        new OnOff($rotateY, 'rotate-y-1', n2_('Rotate') . ' Y', 0, array(
            'relatedFieldsOn' => array(
                'rotate-ysliderrotate-y-2',
                'rotate-ysliderrotate-y-3',
                'rotate-ysliderrotate-y-4'
            )
        ));
        new NumberAutoComplete($rotateY, 'rotate-y-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateY, 'rotate-y-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateY, 'rotate-y-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));

        $rotateZ = new Mixed($rowAnimationRotate, 'rotate-z', false, '0|*|0|*|0|*|0');
        new OnOff($rotateZ, 'rotate-z-1', n2_('Rotate') . ' Z', 0, array(
            'relatedFieldsOn' => array(
                'rotate-zsliderrotate-z-2',
                'rotate-zsliderrotate-z-3',
                'rotate-zsliderrotate-z-4'
            )
        ));
        new NumberAutoComplete($rotateZ, 'rotate-z-2', n2_('Before'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateZ, 'rotate-z-3', n2_('Active'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));
        new NumberAutoComplete($rotateZ, 'rotate-z-4', n2_('After'), '', array(
            'style'  => 'width:30px;',
            'values' => array(
                -60,
                -30,
                0,
                60,
                30
            ),
            'unit'   => '°'
        ));

        $rowSettingsBehavior = new FieldsetRow($tableShowcaseAnimation, 'slider-type-showcase-settings-behavior');

        new OnOff($rowSettingsBehavior, 'carousel', n2_x('Carousel', 'Feature'), 1, array(
            'tipLabel'        => n2_x('Carousel', 'Feature'),
            'tipDescription'  => n2_('This option will create a complete round from your slides if you have enough slides. If you don\'t have enough slides, you could consider duplicating all the slides or just add more slides until you will get a carousel round.'),
            'tipLink'         => 'https://smartslider.helpscoutdocs.com/article/1799-showcase-slider-type#carousel',
            'relatedFieldsOn' => array(
                'slidercontrolsBlockCarouselInteraction'
            )
        ));

        new OnOff($rowSettingsBehavior, 'slide-overlay', n2_('Switch with next/previous slides'), 1, array(
            'tipLabel'       => n2_('Switch with next/previous slides'),
            'tipDescription' => n2_('Clicking on any slide that\'s not in the middle will make the slider switch to that slide. With this option you can disable this behavior, for example, to allow clicking on buttons on the visible slides.'),
        ));

        /**
         * Removing slider settings which are unnecessary for Showcase slider type.
         */

        $form->getElement('/controls/widget-fullscreen')
             ->remove();
        $form->getElement('/size/responsive-mode/responsive-mode-row-1/responsive-mode')
             ->removeOption('fullpage');
        $form->getElement('/size/size/size-2')
             ->remove();

    }
}