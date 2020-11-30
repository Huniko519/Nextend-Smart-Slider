<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Carousel;


use Nextend\Framework\Form\Container\ContainerRowGroup;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\MarginPadding;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Easing;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\TextAutoComplete;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\Framework\Form\Insert\InsertAfter;
use Nextend\Framework\Form\Insert\InsertBefore;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeAdmin;

class SliderTypeCarouselAdmin extends AbstractSliderTypeAdmin {

    protected $ordering = 3;

    public function getLabel() {
        return n2_('Carousel');
    }

    public function getLabelFull() {
        return n2_x('Carousel slider', 'Slider type');
    }

    public function getIcon() {
        return 'ssi_64 ssi_64--carousel';
    }

    public function prepareForm($form) {

        $tableSlideSize = new ContainerTable(new InsertAfter($form->getElement('/size/size')), 'slider-type-carousel-settings-size', n2_('Slide size'));

        $rowSettings = new FieldsetRow($tableSlideSize, 'slider-type-carousel-settings-size-1');

        new NumberAutoComplete($rowSettings, 'slide-width', n2_('Slide width'), 600, array(
            'values' => array(
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
        new NumberAutoComplete($rowSettings, 'slide-height', n2_('Slide height'), 400, array(
            'values' => array(
                300,
                400,
                600,
                800,
                1000
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));


        new NumberAutoComplete($rowSettings, 'maximum-pane-width', n2_('Max pane width'), 3000, array(
            'tipLabel'       => n2_('Max pane width'),
            'tipDescription' => n2_('You can use this option to limit how many slides can show up next to each other.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1786-carousel-slider-type#max-pane-width',
            'values'         => array(
                300,
                600,
                980,
                3000
            ),
            'unit'           => 'px',
            'wide'           => 5
        ));

        new NumberAutoComplete($rowSettings, 'minimum-slide-gap', n2_('Minimum slide distance'), 10, array(
            'tipLabel'       => n2_('Minimum slide distance'),
            'tipDescription' => n2_('The minimum space between two slides.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1786-carousel-slider-type#minimum-slide-distance',
            'values'         => array(
                10,
                50,
                100,
                200
            ),
            'unit'           => 'px',
            'wide'           => 3
        ));

        $spaceGroup = new ContainerRowGroup(new InsertAfter($form->getElement('/general/design/design-1')), 'slider-type-carousel-space', n2_('Side spacing'));

        $rowSpaceDesktop = $spaceGroup->createRow('slider-type-carousel-space-desktop');

        new OnOff($rowSpaceDesktop, 'side-spacing-desktop-enable', n2_('Desktop'), 0, array(
            'relatedFieldsOn' => array(
                'sliderside-spacing-desktop'
            ),
            'tipLabel'        => n2_('Desktop side spacing'),
            'tipDescription'  => n2_('You can create a fix distance between the slider and the slides where your controls are which appear on this device. This way your controls won\'t cover the slide content.')
        ));

        $sideSpacingDesktop = new MarginPadding($rowSpaceDesktop, 'side-spacing-desktop', n2_('Side spacing'), '0|*|0|*|0|*|0', array(
            'unit' => 'px'
        ));
        for ($i = 1; $i < 5; $i++) {
            new NumberAutoComplete($sideSpacingDesktop, 'side-spacing-desktop-' . $i, false, '', array(
                'values' => array(
                    0,
                    20,
                    40,
                    80
                ),
                'wide'   => 3
            ));
        }


        new OnOff($rowSpaceDesktop, 'side-spacing-tablet-enable', n2_('Tablet'), 0, array(
            'relatedFieldsOn' => array(
                'sliderside-spacing-tablet'
            ),
            'tipLabel'        => n2_('Tablet side spacing'),
            'tipDescription'  => n2_('You can create a fix distance between the slider and the slides where your controls are which appear on this device. This way your controls won\'t cover the slide content.')
        ));

        $sideSpacingTablet = new MarginPadding($rowSpaceDesktop, 'side-spacing-tablet', n2_('Side spacing'), '0|*|0|*|0|*|0', array(
            'unit' => 'px'
        ));
        for ($i = 1; $i < 5; $i++) {
            new NumberAutoComplete($sideSpacingTablet, 'side-spacing-tablet-' . $i, false, '', array(
                'values' => array(
                    0,
                    20,
                    40,
                    80
                ),
                'wide'   => 3
            ));
        }


        new OnOff($rowSpaceDesktop, 'side-spacing-mobile-enable', n2_('Mobile'), 0, array(
            'relatedFieldsOn' => array(
                'sliderside-spacing-mobile'
            ),
            'tipLabel'        => n2_('Mobile side spacing'),
            'tipDescription'  => n2_('You can create a fix distance between the slider and the slides where your controls are which appear on this device. This way your controls won\'t cover the slide content.')
        ));

        $sideSpacingMobile = new MarginPadding($rowSpaceDesktop, 'side-spacing-mobile', n2_('Side spacing'), '0|*|0|*|0|*|0', array(
            'unit' => 'px'
        ));
        for ($i = 1; $i < 5; $i++) {
            new NumberAutoComplete($sideSpacingMobile, 'side-spacing-mobile-' . $i, false, '', array(
                'values' => array(
                    0,
                    20,
                    40,
                    80
                ),
                'wide'   => 3
            ));
        }

        $rowGroupSlides = new ContainerRowGroup(new InsertAfter($form->getElement('/slides/slides-design/slides-design-1')), 'slider-type-carousel-group-slides', false);

        $rowSlideBackground = new FieldsetRow($rowGroupSlides, 'slider-type-carousel-background-slide');

        new Color($rowSlideBackground, 'slide-background-color', n2_('Slide background color'), 'ffffffff', array(
            'alpha' => true
        ));

        $rowSliderBackground = new FieldsetRow(new InsertBefore($form->getElement('/general/design/design-1')), 'slider-type-carousel-background-slider');

        new FieldImage($rowSliderBackground, 'background', n2_('Slider background image'), '', array(
            'width'         => '200',
            'relatedFields' => array(
                'sliderbackground-fixed',
                'sliderbackground-size'
            )
        ));
        new OnOff($rowSliderBackground, 'background-fixed', n2_('Fixed'), 0);
        new TextAutoComplete($rowSliderBackground, 'background-size', n2_('Size'), 'cover', array(
            'values' => array(
                'cover',
                'contain',
                'auto'
            )
        ));

        new Color($rowSliderBackground, 'background-color', n2_('Slider background color'), 'dee3e6ff', array(
            'alpha' => true
        ));

        $rowDesignSlide = new FieldsetRow($rowGroupSlides, 'slider-type-carousel-design-slide');
        new Number($rowDesignSlide, 'slide-border-width', n2_('Slide border width'), 0, array(
            'unit'          => 'px',
            'wide'          => 3,
            'relatedFields' => array('sliderslide-border-color')
        ));
        new Color($rowDesignSlide, 'slide-border-color', n2_('Slide border color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new Number($rowDesignSlide, 'slide-border-radius', n2_('Slide border radius'), 0, array(
            'wide' => 3,
            'unit' => 'px'
        ));

        $rowDesignSlider = new FieldsetRow(new InsertAfter($form->getElement('/general/design/design-1')), 'slider-type-carousel-design-slider');

        new Number($rowDesignSlider, 'border-width', n2_('Slider border width'), 0, array(
            'unit'          => 'px',
            'wide'          => 3,
            'relatedFields' => array('sliderborder-color')
        ));
        new Color($rowDesignSlider, 'border-color', n2_('Slider border color'), '3E3E3Eff', array(
            'alpha' => true
        ));
        new Number($rowDesignSlider, 'border-radius', n2_('Slider border radius'), 0, array(
            'unit' => 'px',
            'wide' => 3
        ));

        $tableMainAnimation = new ContainerTable(new InsertBefore($form->getElement('/animations/effects')), 'slider-type-carousel-animation', n2_('Main animation'));

        $rowAnimation1 = new FieldsetRow($tableMainAnimation, 'slider-type-carousel-animation-1');

        $notice = n2_('The Single Switch setting can only move the slides horizontally!') . '<br>';

        new Select($rowAnimation1, 'animation', n2_('Main animation'), 'horizontal', array(
            'options'            => array(
                'no'         => n2_('No'),
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical'),
                'fade'       => n2_('Fade')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'horizontal',
                        'vertical',
                        'fade'
                    ),
                    'field'  => array(
                        'slideranimation-duration',
                        'slideranimation-easing'
                    )
                ),
                array(
                    'values' => array(
                        'horizontal'
                    ),
                    'field'  => array(
                        'slidergrouping-single-switch'
                    )
                )
            )
        ));


        new NumberAutoComplete($rowAnimation1, 'animation-duration', n2_('Duration'), 800, array(
            'values' => array(
                800,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));

        new Easing($rowAnimation1, 'animation-easing', n2_('Easing'), 'easeOutQuad');


        $rowAnimation2 = new FieldsetRow($tableMainAnimation, 'slider-type-carousel-animation-2');

        new OnOff($rowAnimation2, 'carousel', n2_x('Carousel', 'Feature'), 1, array(
            'tipLabel'        => n2_x('Carousel', 'Feature'),
            'tipDescription'  => n2_('This option will create a complete round from your slides if you have enough slides. If you don\'t have enough slides, you could consider duplicating all the slides or just add more slides until you will get a carousel round.'),
            'tipLink'         => 'https://smartslider.helpscoutdocs.com/article/1786-carousel-slider-type#carousel',
            'relatedFieldsOn' => array(
                'slidercontrolsBlockCarouselInteraction'
            )
        ));

        $groupingSingleSwitch = new Grouping($rowAnimation2, 'grouping-single-switch');
        new OnOff($groupingSingleSwitch, 'single-switch', n2_('Single switch'), 0, array(
            'tipLabel'        => n2_('Single switch'),
            'tipDescription'  => n2_('It switches one slide instead of moving all the visible slides.'),
            'tipLink'         => 'https://smartslider.helpscoutdocs.com/article/1786-carousel-slider-type#single-switch',
            'relatedFieldsOn' => array(
                'sliderslider-side-spacing'
            )
        ));

        new Radio($groupingSingleSwitch, 'slider-side-spacing', n2_('Justify slides'), 1, array(
            'options' => array(
                '0' => n2_('Space between'),
                '1' => n2_('Space around'),
                '2' => n2_('Center')
            )
        ));

        /**
         * Removing slider settings which are unnecessary for Carousel slider type.
         */
        $form->getElement('/animations/layer-parallax')
             ->remove();
        $form->getElement('/controls/widget-bar')
             ->remove();
        $form->getElement('/controls/widget-fullscreen')
             ->remove();
        $form->getElement('/size/size/size-2')
             ->remove();


        $form->getElement('/size/responsive-mode/responsive-mode-row-1/responsive-mode')
             ->removeOption('fullpage');


    }
}