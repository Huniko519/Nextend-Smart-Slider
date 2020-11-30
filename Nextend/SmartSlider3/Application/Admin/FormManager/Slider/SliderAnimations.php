<?php


namespace Nextend\SmartSlider3\Application\Admin\FormManager\Slider;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\FormTabbed;
use Nextend\SmartSlider3Pro\Form\Element\Particle;
use Nextend\SmartSlider3Pro\Form\Element\ShapeDivider;

class SliderAnimations extends AbstractSliderTab {

    /**
     * SliderAnimations constructor.
     *
     * @param FormTabbed $form
     */
    public function __construct($form) {
        parent::__construct($form);

        $this->effects();
        $this->layerAnimations();
        $this->layerParallax();
    
    }

    /**
     * @return string
     */
    protected function getName() {
        return 'animations';
    }

    /**
     * @return string
     */
    protected function getLabel() {
        return n2_('Animations');
    }

    protected function effects() {

        /**
         * Used for field injection: /animations/effects
         * Used for field removal: /animations/effects
         */
        $table = new ContainerTable($this->tab, 'effects', n2_('Effects'));

        /**
         * Used for field injection: /animations/effects/effects-row1
         */
        $row = $table->createRow('effects-row1');
        new ShapeDivider($row, 'shape-divider', n2_('Shape divider'));
        new Particle($row, 'particle', n2_('Particle effect'));
    
    }

    protected function layerAnimations() {
        $table = new ContainerTable($this->tab, 'layer-animations', n2_('Layer animations'));
        $row   = $table->createRow('layer-animations');

        new OnOff($row, 'playfirstlayer', n2_('Play on load'), 1, array(
            'tipLabel'       => n2_('Play on load'),
            'tipDescription' => n2_('Plays the layer animations on the first slide when it appears for the first time.')
        ));
        /**
         * Used for field removal: /animations/layer-animations/layer-animations/playonce
         */
        new OnOff($row, 'playonce', n2_('Play once'), 0, array(
            'tipLabel'       => n2_('Play once'),
            'tipDescription' => n2_('Plays the layer animations only during the first loop.')
        ));
        new Select($row, 'layer-animation-play-in', n2_('Play on'), 'end', array(
            'options' => array(
                'start' => n2_('Main animation start'),
                'end'   => n2_('Main animation end')
            )
        ));
        new Select($row, 'layer-animation-play-mode', n2_('Mode'), 'skippable', array(
            'options'        => array(
                'skippable' => n2_('Skippable'),
                'forced'    => n2_('Forced')
            ),
            'tipLabel'       => n2_('Mode'),
            'tipDescription' => n2_('You can make the outgoing layer animations, which don\'t have events, to play on slide switching.'),
        ));
    
    }

    protected function layerParallax() {
        /**
         * Used for field removal: /animations/layer-parallax
         */
        $table = new ContainerTable($this->tab, 'layer-parallax', n2_('Layer parallax'));

        new OnOff($table->getFieldsetLabel(), 'parallax-enabled', n2_('Enable'), 1, array(
            'relatedFieldsOn' => array(
                'table-rows-layer-parallax'
            )
        ));

        $row = $table->createRow('layer-parallax');
        new OnOff($row, 'parallax-enabled-mobile', n2_('Mobile'), 0);
        new OnOff($row, 'parallax-3d', '3D', 0);
        new OnOff($row, 'parallax-animate', n2_('Animate'), 1);
        new Select($row, 'parallax-horizontal', n2_('Horizontal'), 'mouse', array(
            'options' => array(
                '0'            => n2_('Off'),
                'mouse'        => n2_('Mouse'),
                'mouse-invert' => n2_('Mouse') . ' - ' . n2_('Invert')
            )
        ));
        new Select($row, 'parallax-vertical', n2_('Vertical'), 'mouse', array(
            'options' => array(
                '0'             => n2_('Off'),
                'scroll'        => n2_('Scroll'),
                'scroll-invert' => n2_('Scroll') . ' - ' . n2_('Invert'),
                'mouse'         => n2_('Mouse'),
                'mouse-invert'  => n2_('Mouse') . ' - ' . n2_('Invert')
            )
        ));
        new Select($row, 'parallax-mouse-origin', n2_('Mouse origin'), 'slider', array(
            'options' => array(
                'slider' => n2_('Slider center'),
                'enter'  => n2_('Mouse enter position')
            )
        ));
        new Select($row, 'parallax-scroll-move', n2_('Scroll move'), 'both', array(
            'options' => array(
                'both'   => n2_('Both'),
                'bottom' => n2_('To bottom'),
                'top'    => n2_('To top')
            )
        ));
    
    }
}