<?php


namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slide\LayerWindow\Tab;


use Nextend\Framework\Form\Container\LayerWindow\ContainerAnimation;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\Mixed;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Easing;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Element\Text\TextMultiAutoComplete;
use Nextend\Framework\Form\Fieldset\LayerWindow\FieldsetLayerWindow;
class TabAnimation extends AbstractTab {

    /**
     * @return string
     */
    public function getName() {
        return 'animations';
    }

    /**
     * @return string
     */
    public function getLabel() {
        return n2_('Animation');
    }

    /**
     * @return string
     */
    public function getIcon() {
        return 'ssi_24 ssi_24--animation';
    }

    public function display() {
        $containerAnimation = new ContainerAnimation($this->getContainer(), 'animation');

        $containerAnimation->createTab('in', n2_x('In', 'Layer animation'));
        $containerAnimation->createTab('loop', n2_('Loop'));
        $containerAnimation->createTab('out', n2_x('Out', 'Layer animation'));
        $tabEvents = $containerAnimation->createTab('events', n2_x('Events', 'Layer animation'));

        $events = new FieldsetLayerWindow($tabEvents, 'animations-events-events');

        $eventNames = array(
            'layerAnimationPlayIn',
            'layerAnimationPlayLoop',
            'LayerClick',
            'LayerMouseEnter',
            'LayerMouseLeave',
            'SlideClick',
            'SlideMouseEnter',
            'SlideMouseLeave',
            'SliderClick',
            'SliderMouseEnter',
            'SliderMouseLeave'
        );

        new TextMultiAutoComplete($events, 'in-play-event', n2_('Plays in when'), '', array(
            'options' => $eventNames,
            'style'   => 'width:260px;'
        ));

        new TextMultiAutoComplete($events, 'out-play-event', n2_('Plays out when'), '', array(
            'options' => array_merge($eventNames, array(
                'InstantOut',
                'OutForced'
            )),
            'style'   => 'width:260px;'
        ));

        new TextMultiAutoComplete($events, 'loop-play-event', n2_('Plays loop when'), '', array(
            'options' => $eventNames,
            'style'   => 'width:260px;'
        ));

        new TextMultiAutoComplete($events, 'loop-pause-event', n2_('Pauses loop when'), '', array(
            'options' => $eventNames,
            'style'   => 'width:260px;'
        ));

        new TextMultiAutoComplete($events, 'loop-stop-event', n2_('Stops loop when'), '', array(
            'options' => $eventNames,
            'style'   => 'width:260px;'
        ));

        new OnOff($events, 'repeatable', n2_('Repeatable'), 0, array(
            'relatedFieldsOn' => array(
                'layerstart-delay',
                'layerend-delay'
            ),
            'tipLabel'        => n2_('Repeatable'),
            'tipDescription'  => n2_('Allows the layer animations to play more than once.')
        ));

        new NumberAutoComplete($events, 'start-delay', n2_('Start delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));

        new NumberAutoComplete($events, 'end-delay', n2_('End delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new OnOff($events, 'loop-repeat-self-only', n2_('Repeat loop only'), 0, array(
            'tipLabel'       => n2_('Repeat loop only'),
            'tipDescription' => n2_('Allows the stopped loop to start again.')
        ));

        $triggers = new FieldsetLayerWindow($tabEvents, 'animations-events-triggers', n2_('Trigger custom event on'));

        new Text($triggers, 'onclick', n2_('Click'), '', array(
            'style' => 'width:73px;'
        ));
        new Text($triggers, 'onmouseenter', n2_('Mouse enter'), '', array(
            'style' => 'width:73px;'
        ));
        new Text($triggers, 'onmouseleave', n2_('Mouse leave'), '', array(
            'style' => 'width:73px;'
        ));
        new Text($triggers, 'onplay', n2_('Media started'), '', array(
            'style' => 'width:73px;'
        ));
        new Text($triggers, 'onpause', n2_('Media paused'), '', array(
            'style' => 'width:73px;'
        ));
        new Text($triggers, 'onstop', n2_('Media stopped'), '', array(
            'style' => 'width:73px;'
        ));

        $this->formAnimationsBasic();
        $this->formAnimationsReveal();

        parent::display();
    }


    protected function formAnimationsBasic() {

        $basicForm = new FieldsetLayerWindow($this->getContainer(), 'layer-animation-basic-form');
        new NumberAutoComplete($basicForm, '-anim-duration', n2_('Duration'), 500, array(
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new NumberAutoComplete($basicForm, '-anim-delay', n2_('Delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new Easing($basicForm, '-anim-ease', n2_('Easing'), 'easeOutCubic');


        new NumberSlider($basicForm, '-anim-opacity', n2_('Opacity'), 100, array(
            'wide' => 3,
            'min'  => 0,
            'max'  => 100,
            'unit' => '%'
        ));
        new NumberSlider($basicForm, '-anim-n2blur', n2_('Blur'), 0, array(
            'wide' => 3,
            'min'  => 0,
            'max'  => 100,
            'unit' => 'px'
        ));

        $offset = new Grouping($basicForm, 'animation-offset', n2_('Offset'), array(
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));
        new NumberAutoComplete($offset, '-anim-x', false, 0, array(
            'sublabel' => 'X',
            'values'   => array(
                -800,
                -400,
                -200,
                -100,
                -50,
                0,
                50,
                100,
                200,
                400,
                800
            ),
            'unit'     => 'px',
            'style'    => 'width:30px;'
        ));
        new NumberAutoComplete($offset, '-anim-y', false, 0, array(
            'sublabel' => 'Y',
            'values'   => array(
                -800,
                -400,
                -200,
                -100,
                -50,
                0,
                50,
                100,
                200,
                400,
                800
            ),
            'unit'     => 'px',
            'style'    => 'width:30px;'
        ));
        new Number($basicForm, '-anim-z', 'Z', 0, array(
            'wide'  => 4,
            'unit'  => 'px',
            'style' => 'width:30px;'
        ));


        $rotate = new Grouping($basicForm, 'animation-rotate', n2_('Rotate'));
        new NumberAutoComplete($rotate, '-anim-rotationX', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));
        new NumberAutoComplete($rotate, '-anim-rotationY', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));
        new NumberAutoComplete($rotate, '-anim-rotationZ', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        $scale = new Grouping($basicForm, 'animation-scale', n2_('Scale'));
        new NumberAutoComplete($scale, '-anim-scaleX', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'min'      => 0,
            'values'   => array(
                0,
                50,
                100,
                150
            ),
            'unit'     => '%'
        ));
        new NumberAutoComplete($scale, '-anim-scaleY', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'min'      => 0,
            'values'   => array(
                0,
                50,
                100,
                150
            ),
            'unit'     => '%'
        ));


        new Number($basicForm, '-anim-skew', n2_('Skew'), 0, array(
            'wide' => 4,
            'unit' => '%'
        ));

        $layerAnimationBasicFormIn = new FieldsetLayerWindow($this->getContainer(), 'layer-animation-basic-form-in', false);

        $transformOrigin = new Mixed($layerAnimationBasicFormIn, 'basic-in-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new NumberAutoComplete($transformOrigin, 'in-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new NumberAutoComplete($transformOrigin, 'in-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new Number($transformOrigin, 'in-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'unit'     => 'px'
        ));

        new OnOff($layerAnimationBasicFormIn, 'basic-in-special-zero', n2_('Special Zero'), 0, array(
            'tipLabel'       => n2_('Special Zero'),
            'tipDescription' => n2_('Makes the last keyframe to be the origin of the layer animation, instead of its canvas position.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1889-layer-animation'
        ));

        $layerAnimationBasicFormLoop = new FieldsetLayerWindow($this->getContainer(), 'layer-animation-basic-form-loop', false);

        new Number($layerAnimationBasicFormLoop, 'basic-loop-repeat-count', n2_('Repeat count'), 0, array(
            'wide'           => 3,
            'unit'           => n2_('loops'),
            'tipLabel'       => n2_('Repeat count'),
            'tipDescription' => n2_('You can restrict the loop to play only a certain amount of loops, instead of infinite.')
        ));
        new Number($layerAnimationBasicFormLoop, 'basic-loop-repeat-start-delay', n2_('Start delay'), 0, array(
            'wide' => 5,
            'unit' => 'ms'
        ));

        $transformOrigin = new Mixed($layerAnimationBasicFormLoop, 'basic-loop-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new NumberAutoComplete($transformOrigin, 'loop-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new NumberAutoComplete($transformOrigin, 'loop-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new Number($transformOrigin, 'loop-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'unit'     => 'px'
        ));

        $layerAnimationBasicFormOut = new FieldsetLayerWindow($this->getContainer(), 'layer-animation-basic-form-out', false);

        $transformOrigin = new Mixed($layerAnimationBasicFormOut, 'basic-out-transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new NumberAutoComplete($transformOrigin, 'out-transformorigin-x', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new NumberAutoComplete($transformOrigin, 'out-transformorigin-y', false, 50, array(
            'wide'     => 4,
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%'
        ));
        new Number($transformOrigin, 'out-transformorigin-z', false, 0, array(
            'wide'     => 4,
            'sublabel' => 'Z',
            'unit'     => 'px'
        ));
    }

    protected function formAnimationsReveal() {
        $revealForm = new FieldsetLayerWindow($this->getContainer(), 'layer-animation-reveal-form', false);

        new Color($revealForm, '-reveal-color', n2_('Color'), 'ffffff');
        new NumberAutoComplete($revealForm, '-reveal-duration', n2_('Duration'), 500, array(
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));
        new NumberAutoComplete($revealForm, '-reveal-delay', n2_('Delay'), 0, array(
            'min'    => 0,
            'values' => array(
                0,
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms',
            'wide'   => 5
        ));


        $options = array(
            'no'                   => n2_('No'),
            'top'                  => n2_x('Slide', 'Animation') . ' - ' . n2_('Top'),
            'right'                => n2_x('Slide', 'Animation') . ' - ' . n2_('Right'),
            'bottom'               => n2_x('Slide', 'Animation') . ' - ' . n2_('Bottom'),
            'left'                 => n2_x('Slide', 'Animation') . ' - ' . n2_('Left'),
            'skew-top'             => n2_('Skew') . ' - ' . n2_('Top'),
            'skew-right'           => n2_('Skew') . ' - ' . n2_('Right'),
            'skew-bottom'          => n2_('Skew') . ' - ' . n2_('Bottom'),
            'skew-left'            => n2_('Skew') . ' - ' . n2_('Left'),
            'curtains-horizontal'  => n2_('Curtains') . ' - ' . n2_('Horizontal'),
            'curtains-vertical'    => n2_('Curtains') . ' - ' . n2_('Vertical'),
            'curtains-diagonal-1'  => n2_('Curtains') . ' - ' . n2_('Diagonal') . ' 1',
            'curtains-diagonal-2'  => n2_('Curtains') . ' - ' . n2_('Diagonal') . ' 2',
            'rotate-top-left'      => n2_('Rotate') . ' - ' . n2_('Top') . ' ' . n2_('Left') . ' 1',
            'rotate-top-left-'     => n2_('Rotate') . ' - ' . n2_('Top') . ' ' . n2_('Left') . ' 2',
            'rotate-top-right'     => n2_('Rotate') . ' - ' . n2_('Top') . ' ' . n2_('Right') . ' 1',
            'rotate-top-right-'    => n2_('Rotate') . ' - ' . n2_('Top') . ' ' . n2_('Right') . ' 2',
            'rotate-bottom-right'  => n2_('Rotate') . ' - ' . n2_('Bottom') . ' ' . n2_('Right') . ' 1',
            'rotate-bottom-right-' => n2_('Rotate') . ' - ' . n2_('Bottom') . ' ' . n2_('Right') . ' 2',
            'rotate-bottom-left'   => n2_('Rotate') . ' - ' . n2_('Bottom') . ' ' . n2_('Left') . ' 1',
            'rotate-bottom-left-'  => n2_('Rotate') . ' - ' . n2_('Bottom') . ' ' . n2_('Left') . ' 2',
            'circle-top'           => n2_('Circle') . ' - ' . n2_('Top'),
            'circle-right'         => n2_('Circle') . ' - ' . n2_('Right'),
            'circle-bottom'        => n2_('Circle') . ' - ' . n2_('Bottom'),
            'circle-left'          => n2_('Circle') . ' - ' . n2_('Left'),
        );

        new Select($revealForm, '-reveal-from', n2_('From'), 'top', array(
            'options' => $options
        ));
        new Easing($revealForm, '-reveal-from-ease', n2_('Easing'), 'easeOutCubic');

        unset($options['no']);
        new Select($revealForm, '-reveal-to', n2_('To'), 'bottom', array(
            'options' => $options
        ));
        new Easing($revealForm, '-reveal-to-ease', n2_('Easing'), 'easeOutCubic');

        new Select($revealForm, '-reveal-content', n2_('Content'), '', array(
            'options' => array(
                ''           => n2_('Default'),
                'fade'       => n2_('Fade'),
                'scale-up'   => n2_('Scale up'),
                'scale-down' => n2_('Scale down'),
                'top'        => n2_('Top'),
                'right'      => n2_('Right'),
                'bottom'     => n2_('Bottom'),
                'left'       => n2_('Left')
            )
        ));
    }
}
