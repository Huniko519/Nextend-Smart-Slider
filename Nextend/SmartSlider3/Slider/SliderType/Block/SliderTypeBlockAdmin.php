<?php

namespace Nextend\SmartSlider3\Slider\SliderType\Block;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\TextAutoComplete;
use Nextend\Framework\Form\Element\Text\Video;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\Framework\Form\Fieldset\LayerWindow\FieldsetLayerWindow;
use Nextend\Framework\Form\Insert\InsertAfter;
use Nextend\Framework\Form\Insert\InsertBefore;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeAdmin;
use Nextend\SmartSlider3Pro\Form\Element\PostBackgroundAnimation;
use Nextend\SmartSlider3Pro\PostBackgroundAnimation\PostBackgroundAnimationManager;

class SliderTypeBlockAdmin extends AbstractSliderTypeAdmin {

    protected $ordering = 2;

    public function getLabel() {
        return n2_('Block');
    }

    public function getIcon() {
        return 'ssi_64 ssi_64--block';
    }

    public function prepareForm($form) {


        $form->getElement('/autoplay')
             ->remove();
        $tableBackground = new ContainerTable(new InsertBefore($form->getElement('/animations/effects')), 'slider-type-block-background', n2_('Background animation'));

        $rowKenBurns = new FieldsetRow($tableBackground, 'slider-type-block-kenburns');

        new PostBackgroundAnimation($rowKenBurns, 'kenburns-animation', n2_('Ken Burns effect'), '50|*|50|*|', array(
            'relatedFields' => array(
                'sliderkenburns-animation-speed',
                'sliderkenburns-animation-strength'
            )
        ));

        new Select($rowKenBurns, 'kenburns-animation-speed', n2_('Speed'), 'default', array(
            'options' => array(
                'default'   => n2_('Default'),
                'superSlow' => n2_('Super slow') . ' 0.25x',
                'slow'      => n2_('Slow') . ' 0.5x',
                'normal'    => n2_('Normal') . ' 1x',
                'fast'      => n2_('Fast') . ' 2x',
                'superFast' => n2_('Super fast') . ' 4x'
            )
        ));

        new Select($rowKenBurns, 'kenburns-animation-strength', n2_('Strength'), 'default', array(
            'options' => array(
                'default'     => n2_('Default'),
                'superSoft'   => n2_('Super soft') . ' 0.3x',
                'soft'        => n2_('Soft') . ' 0.6x',
                'normal'      => n2_('Normal') . ' 1x',
                'strong'      => n2_('Strong') . ' 1.5x',
                'superStrong' => n2_('Super strong') . ' 2x'
            )
        ));

        $rowSettings = new FieldsetRow(new InsertBefore($form->getElement('/general/design/design-1')), 'slider-type-block-settings');

        new FieldImage($rowSettings, 'background', n2_('Slider background image'), '', array(
            'width'         => '200',
            'relatedFields' => array(
                'sliderbackground-fixed',
                'sliderbackground-size'
            )
        ));
        new OnOff($rowSettings, 'background-fixed', n2_('Fixed'), 0);
        new TextAutoComplete($rowSettings, 'background-size', n2_('Size'), 'cover', array(
            'values' => array(
                'cover',
                'contain',
                'auto'
            )
        ));

        new Video($rowSettings, 'backgroundVideoMp4', 'Slider background video', '', array(
            'relatedFields' => array(
                'sliderbackgroundVideoMuted',
                'sliderbackgroundVideoLoop',
                'sliderbackgroundVideoMode'
            )
        ));
        new OnOff($rowSettings, 'backgroundVideoMuted', n2_('Muted'), 1);
        new OnOff($rowSettings, 'backgroundVideoLoop', n2_x('Loop', 'Video/Audio play'), 1);
        new Select($rowSettings, 'backgroundVideoMode', n2_('Fill mode'), 'fill', array(
            'options' => array(
                'fill'   => n2_('Fill'),
                'fit'    => n2_('Fit'),
                'center' => n2_('Center')
            )
        ));

        $rowOther = new FieldsetRow(new InsertAfter($form->getElement('/general/design/design-1')), 'slider-type-block-other');
        new Textarea($rowOther, 'slider-css', n2_('Slider') . ' CSS', '', array(
            'height' => 26,
            'resize' => 'both'
        ));
    

        /**
         * Removing slider settings which are unnecessary for Block slider type.
         */
        $form->getElement('/controls/general')
             ->remove();
        $form->getElement('/general/alias/alias-1/alias-slideswitch')
             ->remove();
        $form->getElement('/controls/widget-arrow')
             ->remove();
        $form->getElement('/controls/widget-bullet')
             ->remove();
        $form->getElement('/controls/widget-bar')
             ->remove();
        $form->getElement('/controls/widget-thumbnail')
             ->remove();
        $form->getElement('/animations/layer-animations/layer-animations/playonce')
             ->remove();
        $form->getElement('/optimize/optimize-lazyload')
             ->remove();
        $form->getElement('/slides/slides-randomize')
             ->remove();
        $form->getElement('/slides/other')
             ->remove();
        $form->getElement('/developer/developer/developer-1/controlsBlockCarouselInteraction')
             ->remove();
        $form->getElement('/controls/widget-fullscreen')
             ->remove();
    

    }

    public function renderSlideFields($container) {
        $dataToFields = array();

        $tableAnimation = new FieldsetLayerWindow($container, 'fields-slide-animation', n2_('Animation'));

        PostBackgroundAnimationManager::enqueue($container->getForm());

        $rowKenBurns = new Grouping($tableAnimation, 'slide-settings-animation-ken-burns');
        new PostBackgroundAnimation($rowKenBurns, 'kenburns-animation', n2_('Ken Burns effect'), '', array(
            'relatedFields' => array(
                'layerkenburns-animation-speed',
                'layerkenburns-animation-strength'
            )
        ));
        $dataToFields[] = [
            'name' => 'kenburns-animation',
            'id'   => 'layerkenburns-animation',
            'def'  => '50|*|50|*'
        ];

        new Select($rowKenBurns, 'kenburns-animation-speed', n2_('Speed'), '', array(
            'options' => array(
                'default'   => n2_('Default'),
                'superSlow' => n2_('Super slow') . ' 0.25x',
                'slow'      => n2_('Slow') . ' 0.5x',
                'normal'    => n2_('Normal') . ' 1x',
                'fast'      => n2_('Fast') . ' 2x',
                'superFast' => n2_('Super fast' . ' 4x')
            )
        ));
        $dataToFields[] = [
            'name' => 'kenburns-animation-speed',
            'id'   => 'layerkenburns-animation-speed',
            'def'  => 'default'
        ];

        new Select($rowKenBurns, 'kenburns-animation-strength', n2_('Strength'), '', array(
            'options' => array(
                'default'     => n2_('Default'),
                'superSoft'   => n2_('Super soft') . ' 0.3x',
                'soft'        => n2_('Soft') . ' 0.6x',
                'normal'      => n2_('Normal') . ' 1x',
                'strong'      => n2_('Strong') . ' 1.5x',
                'superStrong' => n2_('Super strong') . ' 2x'
            )
        ));
        $dataToFields[] = [
            'name' => 'kenburns-animation-strength',
            'id'   => 'layerkenburns-animation-strength',
            'def'  => 'default'
        ];

        Js::addInline("N2R('SectionSlide', function(){ N2Classes.SectionSlide.addExternalDataToField(" . json_encode($dataToFields) . ");});");
    

    }

    public function registerSlideAdminProperties($component) {
        $component->createProperty('kenburns-animation', '50|*|50|*|');
        $component->createProperty('kenburns-animation-speed', 'default');
        $component->createProperty('kenburns-animation-strength', 'default');
    
    }
}