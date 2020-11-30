<?php

namespace Nextend\SmartSlider3\Slider\SliderType\Block;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Sanitize;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;

class SliderTypeBlockFrontend extends AbstractSliderTypeFrontend {

    public function getDefaults() {
        return array(
            'background'       => '',
            'background-size'  => 'cover',
            'background-fixed' => 0,
            'slider-css'       => '',

            'kenburns-animation' => ''
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;

        Js::addStaticGroup(SliderTypeBlock::getAssetsPath() . '/dist/smartslider-block-type-frontend.min.js', 'smartslider-block-type-frontend');

        $this->jsDependency[] = 'smartslider-block-type-frontend';

        $background = $params->get('background');
        $sliderCSS  = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS = 'background-image: URL(' . ResourceTranslator::toUrl($background) . ');';
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();
        ?>

        <div class="n2-ss-slider-1 n2-ow" style="<?php echo Sanitize::esc_attr($sliderCSS); ?>">
            <div class="n2-ss-slider-2 n2-ow">
                <?php
                echo $this->getBackgroundVideo($params);
                ?>
                <?php
                echo $this->slider->staticHtml;

                echo Html::tag('div', array('class' => 'n2-ss-slide-backgrounds'));

                $slide = $this->slider->getActiveSlide();

                $slide->finalize();

                echo Html::tag('div', Html::mergeAttributes($slide->attributes, $slide->linkAttributes, array(
                    'class' => 'n2-ss-slide n2-ss-canvas n2-ow ' . $slide->classes,
                    'style' => $slide->style
                )), $slide->background . $slide->getHTML());
                ?>
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

        $this->style .= $css->getCSS();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderBlock('#{$this->slider->elementId}', " . $this->encodeJavaScriptProperties() . ");});";
    }

    private function getBackgroundVideo($params) {
        $mp4 = ResourceTranslator::toUrl($params->get('backgroundVideoMp4', ''));

        if (empty($mp4)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= Html::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ), '', false);
        }

        $attributes = array();

        if ($params->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($params->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        return Html::tag('div', array('class' => 'n2-ss-slider-background-video-container n2-ow'), Html::tag('video', $attributes + array(
                'class'              => 'n2-ss-slider-background-video n2-ow',
                'data-mode'          => $params->get('backgroundVideoMode', 'fill'),
                'playsinline'        => 1,
                'webkit-playsinline' => 1,
                'data-keepplaying'   => 1,
                'preload'            => 'none'
            ), $sources));

    }

    /**
     * @param $params Data
     */
    public function limitParams($params) {

        $params->loadArray(array(
            'controlsScroll'            => 0,
            'controlsDrag'              => 0,
            'controlsTouch'             => 0,
            'controlsKeyboard'          => 0,
            'blockCarouselInteraction'  => 1,
            'autoplay'                  => 0,
            'autoplayStart'             => 0,
            'widget-arrow-enabled'      => 0,
            'widget-bullet-enabled'     => 0,
            'widget-autoplay-enabled'   => 0,
            'widget-indicator-enabled'  => 0,
            'widget-bar-enabled'        => 0,
            'widget-thumbnail-enabled'  => 0,
            'widget-fullscreen-enabled' => 0,
            'randomize'                 => 0,
            'randomizeFirst'            => 0,
            'randomize-cache'           => 0,
            'maximumslidecount'         => 1,
            'imageload'                 => 0,
            'imageloadNeighborSlides'   => 0,
            'maintain-session'          => 0,
            'global-lightbox'           => 0
        ));
    }
}