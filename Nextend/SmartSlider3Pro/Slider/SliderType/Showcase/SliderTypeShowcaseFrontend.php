<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Showcase;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Sanitize;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;

class SliderTypeShowcaseFrontend extends AbstractSliderTypeFrontend {

    private $direction = 'horizontal';

    public function getDefaults() {
        return array(
            'slide-width'         => 600,
            'slide-height'        => 400,
            'background'          => '',
            'background-size'     => 'cover',
            'background-fixed'    => 0,
            'border-width'        => 0,
            'border-color'        => '3E3E3Eff',
            'border-radius'       => 0,
            'slider-css'          => '',
            'slide-css'           => '',
            'animation-duration'  => 800,
            'animation-easing'    => 'easeOutQuad',
            'animation-direction' => 'horizontal',
            'slide-distance'      => 60,
            'perspective'         => 1000,
            'carousel'            => 1,
            'carousel-slides'     => 3,
            'opacity'             => '0|*|100|*|100|*|100',
            'scale'               => '0|*|100|*|100|*|100',
            'translate-x'         => '0|*|0|*|0|*|0',
            'translate-y'         => '0|*|0|*|0|*|0',
            'translate-z'         => '0|*|0|*|0|*|0',
            'rotate-x'            => '0|*|0|*|0|*|0',
            'rotate-y'            => '0|*|0|*|0|*|0',
            'rotate-z'            => '0|*|0|*|0|*|0'
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;

        Js::addStaticGroup(SliderTypeShowcase::getAssetsPath() . '/dist/smartslider-showcase-type-frontend.min.js', 'smartslider-showcase-type-frontend');

        $this->jsDependency[] = 'smartslider-showcase-type-frontend';

        $background      = $params->get('background');
        $backgroundColor = $params->get('background-color', '');
        $sliderCSS       = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS .= 'background-image: URL(' . ResourceTranslator::toUrl($background) . ');';
        }
        if (!empty($backgroundColor)) {
            $rgba = Color::hex2rgba($backgroundColor);
            if ($rgba[3] != 0) {
                $sliderCSS .= 'background-color:RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ');';
            }
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();

        $overlay = $params->get('slide-overlay', 1);
        ?>
        <div class="n2-ss-slider-1 n2_ss__touch_element n2-ow">
            <div class="n2-ss-slider-2 n2-ow" style="<?php echo Sanitize::esc_attr($sliderCSS); ?>">
                <div class="n2-ss-slider-3 n2-ow">
                    <?php
                    echo $this->slider->staticHtml;
                    ?>
                    <div class="n2-ss-showcase-slides n2-ow"><?php
                        foreach ($this->slider->getSlides() as $i => $slide) {
                            $slide->finalize();

                            echo Html::tag('div', Html::mergeAttributes($slide->attributes, array(
                                'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                'style' => $slide->style . $params->get('slide-css')
                            )), $slide->background . Html::tag('div', array('class' => 'n2-ss-slide-inner') + $slide->linkAttributes, $slide->getHTML()) . ($overlay ? Html::tag('div', array('class' => 'n2-ss-showcase-overlay n2-ow')) : ''));
                        }
                        ?></div>
                </div>
                <?php
                $this->renderShapeDividers();
                ?>
            </div>
            <?php
            $this->widgets->echoRemainder();
            ?>
        </div>
        <?php
        $this->widgets->echoBelow();
        echo $this->closeSliderElement();

        $this->javaScriptProperties['carousel']           = intval($params->get('carousel'));
        $this->javaScriptProperties['carouselSideSlides'] = intval((max(intval($params->get('carousel-slides')), 1) - 1) / 2);

        $this->javaScriptProperties['showcase'] = array(
            'duration' => intval($params->get('animation-duration')),
            'ease'     => $params->get('animation-easing')
        );

        $this->initAnimationProperties();

        $this->style .= $css->getCSS();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderShowcase('#{$this->slider->elementId}', " . $this->encodeJavaScriptProperties() . ");});";
    }

    protected function getSliderClasses() {
        switch ($this->slider->params->get('animation-direction', 'horizontal')) {
            case 'vertical':
                $this->direction = 'vertical';

                return parent::getSliderClasses() . ' n2-ss-showcase-vertical';
                break;
            default:
                $this->direction = 'horizontal';

                return parent::getSliderClasses() . ' n2-ss-showcase-horizontal';
        }
    }

    private function initAnimationProperties() {
        $params = $this->slider->params;

        $slideDistance = intval($params->get('slide-distance'));

        $this->javaScriptProperties['showcase'] += array(
            'direction' => $this->direction,
            'distance'  => $slideDistance,
            'animate'   => array(
                'opacity'   => self::animationPropertyState($params, 'opacity', 100),
                'scale'     => self::animationPropertyState($params, 'scale', 100),
                'x'         => self::animationPropertyState($params, 'translate-x'),
                'y'         => self::animationPropertyState($params, 'translate-y'),
                'z'         => self::animationPropertyState($params, 'translate-z'),
                'rotationX' => self::animationPropertyState($params, 'rotate-x'),
                'rotationY' => self::animationPropertyState($params, 'rotate-y'),
                'rotationZ' => self::animationPropertyState($params, 'rotate-z'),
            ),
            'overlay'   => $params->get('slide-overlay', 1)
        );
    }

    private static function animationPropertyState($params, $prop, $normalize = 1) {
        $propValue = Common::parse($params->get($prop));
        if ($propValue[0] != 1) {
            return null;
        }

        return array(
            'before' => intval($propValue[1]) / $normalize,
            'active' => intval($propValue[2]) / $normalize,
            'after'  => intval($propValue[3]) / $normalize
        );
    }

    /**
     * @param $params Data
     */
    public function limitParams($params) {
        $limitParams = array(
            'widget-fullscreen-enabled' => 0,
            'responsiveLimitSlideWidth' => 0
        );

        if ($params->get('responsive-mode') === 'fullpage') {
            $limitParams['responsive-mode'] = 'auto';
        }

        $params->loadArray($limitParams);
    }
}