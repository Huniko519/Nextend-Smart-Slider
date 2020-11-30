<?php


namespace Nextend\SmartSlider3Pro\LayerAnimation;


use Nextend\Framework\Pattern\SingletonTrait;

class LayerAnimationStorage {

    use SingletonTrait;

    protected $data = array(
        'in'   => array(),
        'loop' => array(),
        'out'  => array()
    );

    protected function init() {

        $this->data['in']   = array(
            'fade'    => array(
                'icon'  => 'ssi_24--fade',
                'label' => n2_('Fade'),
                'a'     => $this->inFade()
            ),
            'move'    => array(
                'icon'  => 'ssi_24--move',
                'label' => n2_('Move'),
                'a'     => $this->inMove()
            ),
            'reveal'  => array(
                'icon'  => 'ssi_24--reveal',
                'label' => n2_('Reveal'),
                'a'     => $this->inReveal()
            ),
            'scale'   => array(
                'icon'  => 'ssi_24--scale',
                'label' => n2_('Scale'),
                'a'     => $this->inScale()
            ),
            'flip'    => array(
                'icon'  => 'ssi_24--flip',
                'label' => n2_('Flip'),
                'a'     => $this->inFlip()
            ),
            'rotate'  => array(
                'icon'  => 'ssi_24--rotate',
                'label' => n2_('Rotate'),
                'a'     => $this->inRotate()
            ),
            'bounce'  => array(
                'icon'  => 'ssi_24--bounce',
                'label' => n2_('Bounce'),
                'a'     => $this->inBounce()
            ),
            'special' => array(
                'icon'  => 'ssi_24--special',
                'label' => n2_('Special'),
                'a'     => $this->inSpecial()
            )
        );
        $this->data['loop'] = array(
            'special' => array(
                'icon'  => 'ssi_24--special',
                'label' => n2_('Special'),
                'a'     => $this->loopSpecial()
            )
        );
        $this->data['out']  = array(
            'fade' => array(
                'icon'  => 'ssi_24--fade',
                'label' => n2_('Fade'),
                'a'     => $this->outFade()
            ),
        );
    }

    /**
     * @return string
     */
    public function getData() {

        return json_encode($this->data);
    }

    private function inFade() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Blur'),
                'keyFrames' => array(
                    array(
                        'n2blur'  => 10,
                        'opacity' => 0
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Left fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0,
                        'x'       => 400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Right fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0,
                        'x'       => -400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Top fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0,
                        'y'       => 400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bottom fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0,
                        'y'       => -400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Flash'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.25,
                        'opacity'  => 1
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.25,
                        'opacity'  => 0
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.25,
                        'opacity'  => 1
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.25,
                        'opacity'  => 0
                    )
                )
            ),
        );
    }

    private function inMove() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Left'),
                'keyFrames' => array(
                    array(
                        'x' => 400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Right'),
                'keyFrames' => array(
                    array(
                        'x' => -400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Top'),
                'keyFrames' => array(
                    array(
                        'y' => 400
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bottom'),
                'keyFrames' => array(
                    array(
                        'y' => -400
                    )
                )
            )
        );
    }

    private function inReveal() {
        return array(
            array(
                'type' => 'reveal',
                'name' => n2_('Left to Right'),
                'data' => array(
                    'from' => 'left',
                    'to'   => 'right'
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Top to Bottom'),
                'data' => array(
                    'from' => 'top',
                    'to'   => 'bottom'
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Skew Left to Right'),
                'data' => array(
                    'from' => 'skew-left',
                    'to'   => 'skew-right'
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Curtains'),
                'data' => array(
                    'from' => 'curtains-horizontal',
                    'to'   => 'curtains-horizontal'
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Rotate'),
                'data' => array(
                    'from' => 'rotate-top-left',
                    'to'   => 'rotate-top-left-'
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Circle'),
                'data' => array(
                    'from' => 'circle-left',
                    'to'   => 'circle-right'
                )
            )
        );
    }

    private function inScale() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Downscale'),
                'keyFrames' => array(
                    array(
                        'scaleX'  => 2,
                        'scaleY'  => 2,
                        'opacity' => 0
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Upscale'),
                'keyFrames' => array(
                    array(
                        'scaleX' => 0,
                        'scaleY' => 0
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Downscale back out'),
                'keyFrames' => array(
                    array(
                        'ease'    => 'easeOutBack',
                        'opacity' => 0,
                        'scaleX'  => 1.2,
                        'scaleY'  => 1.2
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Upscale back out'),
                'keyFrames' => array(
                    array(
                        'ease'    => 'easeOutBack',
                        'opacity' => 0,
                        'scaleX'  => 0.8,
                        'scaleY'  => 0.8
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Pulse'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.5
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.5,
                        'scaleX'   => 1.05,
                        'scaleY'   => 1.05
                    )
                )
            ),
        );
    }

    private function inFlip() {
        return array(
            array(
                'type'            => 'basic',
                'name'            => n2_('Flip left'),
                'transformOrigin' => '0|*|50|*|0',
                'keyFrames'       => array(
                    array(
                        'opacity'   => 0,
                        'rotationY' => -90
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Flip right'),
                'transformOrigin' => '100|*|50|*|0',
                'keyFrames'       => array(
                    array(
                        'opacity'   => 0,
                        'rotationY' => 90
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Flip down'),
                'transformOrigin' => '50|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'opacity'   => 0,
                        'rotationX' => 90
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Flip up'),
                'transformOrigin' => '50|*|100|*|0',
                'keyFrames'       => array(
                    array(
                        'opacity'   => 0,
                        'rotationX' => -90
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Flip in X'),
                'keyFrames' => array(
                    array(
                        'duration'  => 0.4,
                        'opacity'   => 0,
                        'rotationY' => -90
                    ),
                    array(
                        'duration'  => 0.2,
                        'opacity'   => 0.5,
                        'rotationY' => 20
                    ),
                    array(
                        'duration'  => 0.2,
                        'opacity'   => 1,
                        'rotationY' => -10
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationY' => 5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Flip in Y'),
                'keyFrames' => array(
                    array(
                        'duration'  => 0.4,
                        'opacity'   => 0,
                        'rotationX' => -90
                    ),
                    array(
                        'duration'  => 0.2,
                        'opacity'   => 0.5,
                        'rotationX' => 20
                    ),
                    array(
                        'duration'  => 0.2,
                        'opacity'   => 1,
                        'rotationX' => -10
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationX' => 5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Flap'),
                'keyFrames' => array(
                    array(
                        'duration'  => 0.5,
                        'opacity'   => 0,
                        'rotationX' => 90
                    ),
                    array(
                        'duration'  => 0.5,
                        'opacity'   => 1,
                        'rotationX' => -50
                    )
                )
            )
        );
    }

    private function inRotate() {
        return array(
            array(
                'type'            => 'basic',
                'name'            => n2_('Rotate top left'),
                'transformOrigin' => '0|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'duration'  => 1,
                        'opacity'   => 0,
                        'rotationZ' => 90
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Rotate top right'),
                'transformOrigin' => '100|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'duration'  => 1,
                        'opacity'   => 0,
                        'rotationZ' => -90
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Roll in'),
                'keyFrames' => array(
                    array(
                        'duration'  => 1,
                        'x'         => 500,
                        'rotationZ' => 360
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Rotate top left back out'),
                'transformOrigin' => '0|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'ease'      => 'easeOutBack',
                        'rotationZ' => 180
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Rotate all axis'),
                'transformOrigin' => '0|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'opacity'   => 0,
                        'rotationX' => 90,
                        'rotationY' => 20,
                        'rotationZ' => 20
                    )
                )
            )
        );
    }

    private function inBounce() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.18
                    ),
                    array(
                        'ease'     => 'easeInQuint',
                        'duration' => 0.18,
                        'y'        => 30
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.15
                    ),
                    array(
                        'ease'     => 'easeInQuint',
                        'duration' => 0.15,
                        'y'        => 15
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.12
                    ),
                    array(
                        'ease'     => 'easeInQuint',
                        'duration' => 0.12,
                        'y'        => 8
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce in'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2,
                        'opacity'  => 0,
                        'scaleX'   => 0.3,
                        'scaleY'   => 0.3
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2,
                        'opacity'  => .33,
                        'scaleX'   => 1.1,
                        'scaleY'   => 1.1
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2,
                        'opacity'  => .66,
                        'scaleX'   => .9,
                        'scaleY'   => .9
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2,
                        'opacity'  => 1,
                        'scaleX'   => 1.03,
                        'scaleY'   => 1.03
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2,
                        'opacity'  => 1,
                        'scaleX'   => .97,
                        'scaleY'   => .97
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce in left'),
                'keyFrames' => array(
                    array(
                        'duration' => 0.6,
                        'opacity'  => 0,
                        'x'        => 3000
                    ),
                    array(
                        'duration' => 0.15,
                        'opacity'  => 1,
                        'x'        => -25
                    ),
                    array(
                        'duration' => 0.15,
                        'x'        => 10
                    ),
                    array(
                        'duration' => 0.15,
                        'x'        => -5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce in right'),
                'keyFrames' => array(
                    array(
                        'duration' => 0.6,
                        'opacity'  => 0,
                        'x'        => -3000
                    ),
                    array(
                        'duration' => 0.15,
                        'opacity'  => 1,
                        'x'        => 25
                    ),
                    array(
                        'duration' => 0.15,
                        'x'        => -10
                    ),
                    array(
                        'duration' => 0.15,
                        'x'        => 5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce in down'),
                'keyFrames' => array(
                    array(
                        'duration' => 0.6,
                        'opacity'  => 0,
                        'y'        => 3000
                    ),
                    array(
                        'duration' => 0.15,
                        'opacity'  => 1,
                        'y'        => -25
                    ),
                    array(
                        'duration' => 0.15,
                        'y'        => 10
                    ),
                    array(
                        'duration' => 0.15,
                        'y'        => -5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Bounce in up'),
                'keyFrames' => array(
                    array(
                        'duration' => 0.6,
                        'opacity'  => 0,
                        'y'        => -3000
                    ),
                    array(
                        'duration' => 0.15,
                        'opacity'  => 1,
                        'y'        => 25
                    ),
                    array(
                        'duration' => 0.15,
                        'y'        => -10
                    ),
                    array(
                        'duration' => 0.15,
                        'y'        => 5
                    )
                )
            )
        );
    }

    private function inSpecial() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Rubber band'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.3
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'scaleX'   => 1.25,
                        'scaleY'   => 0.75
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'scaleX'   => 0.75,
                        'scaleY'   => 1.25
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.15,
                        'scaleX'   => 1.15,
                        'scaleY'   => 0.85
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'scaleX'   => 0.95,
                        'scaleY'   => 1.05
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.25,
                        'scaleX'   => 1.05,
                        'scaleY'   => 0.95
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Shake'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => 10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => -10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => 10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => -10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => 10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => -10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => 10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => -10
                    ),
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1,
                        'x'        => 10
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Swing'),
                'transformOrigin' => '50|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.2
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationZ' => -15
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationZ' => 10
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationZ' => -5
                    ),
                    array(
                        'duration'  => 0.2,
                        'rotationZ' => 5
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Wooble'),
                'keyFrames' => array(
                    array(
                        'ease'     => 'easeOutCubic',
                        'duration' => 0.1
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 0.9,
                        'scaleY'    => 0.9,
                        'rotationZ' => 3
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 0.9,
                        'scaleY'    => 0.9,
                        'rotationZ' => 3
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => -3
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => 3,
                        'x'         => -10
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => -3,
                        'x'         => 10
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => 3,
                        'x'         => -10
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => -3,
                        'x'         => 10
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => 3
                    ),
                    array(
                        'duration'  => 0.1,
                        'scaleX'    => 1.1,
                        'scaleY'    => 1.1,
                        'rotationZ' => -3
                    )
                )
            )
        );
    }

    private function loopSpecial() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Pulse'),
                'keyFrames' => array(
                    array(
                        'duration' => .5,
                        'scaleX'   => 1.05,
                        'scaleY'   => 1.05
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Shrink'),
                'keyFrames' => array(
                    array(
                        'duration' => .5,
                        'scaleX'   => .8,
                        'scaleY'   => .8
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_x('Slide', 'Animation'),
                'keyFrames' => array(
                    array(
                        'duration' => .5,
                        'x'        => 200
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Roll'),
                'keyFrames' => array(
                    array(
                        'ease'      => 'linear',
                        'duration'  => 1,
                        'rotationZ' => 360
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Grow rotate'),
                'keyFrames' => array(
                    array(
                        'duration'  => 0.5,
                        'rotationZ' => 10,
                        'scaleX'    => 1.15,
                        'scaleY'    => 1.15
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Skew'),
                'keyFrames' => array(
                    array(
                        'duration' => 0.5,
                        'skewX'    => -15
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Swing'),
                'transformOrigin' => '50|*|0|*|0',
                'keyFrames'       => array(
                    array(
                        'duration'  => 0.5,
                        'rotationZ' => 10
                    ),
                    array(
                        'duration'  => 0.5,
                        'rotationZ' => -10
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Pendulum'),
                'transformOrigin' => '50|*|-300|*|0',
                'keyFrames'       => array(
                    array(
                        'duration'  => 0.5,
                        'rotationZ' => 10
                    ),
                    array(
                        'duration'  => 0.5,
                        'rotationZ' => -10
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Pendulum 3D'),
                'transformOrigin' => '50|*|-80|*|20',
                'keyFrames'       => array(
                    array(
                        'duration'  => 2,
                        'x'         => 30,
                        'rotationX' => 8,
                        'rotationY' => 10
                    ),
                    array(
                        'duration'  => 2,
                        'x'         => -30,
                        'rotationX' => 8,
                        'rotationY' => -10
                    )
                )
            ),
            array(
                'type'            => 'basic',
                'name'            => n2_('Vertical pendulum 3D'),
                'transformOrigin' => '-80|*|50|*|20',
                'keyFrames'       => array(
                    array(
                        'duration'  => 2,
                        'y'         => 30,
                        'rotationX' => -10
                    ),
                    array(
                        'duration'  => 2,
                        'y'         => -30,
                        'rotationX' => 10
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Shake'),
                'keyFrames' => array(
                    array(
                        'duration' => .05,
                        'x'        => 10
                    ),
                    array(
                        'duration' => .05,
                        'x'        => -10
                    ),
                    array(
                        'duration'  => .05,
                        'x'         => 10,
                        'rotationZ' => 3
                    ),
                    array(
                        'duration'  => .05,
                        'y'         => 10,
                        'rotationZ' => -3
                    ),
                    array(
                        'duration'  => .05,
                        'x'         => 10,
                        'rotationZ' => -2
                    ),
                    array(
                        'duration'  => .05,
                        'x'         => 10,
                        'y'         => -5,
                        'rotationZ' => 3
                    )
                )
            )
        );
    }

    private function outFade() {
        return array(
            array(
                'type'      => 'basic',
                'name'      => n2_('Fade'),
                'keyFrames' => array(
                    array(
                        'opacity' => 0
                    )
                )
            ),
            array(
                'type'      => 'basic',
                'name'      => n2_('Blur'),
                'keyFrames' => array(
                    array(
                        'n2blur'  => 10,
                        'opacity' => 0
                    )
                )
            ),
            array(
                'type' => 'reveal',
                'name' => n2_('Reveal'),
                'data' => array(
                    'from' => 'left',
                    'to'   => 'right'
                )
            )
        );
    }
}