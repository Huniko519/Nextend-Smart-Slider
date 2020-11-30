<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Carousel;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Sanitize;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;

class SliderTypeCarouselFrontend extends AbstractSliderTypeFrontend {

    public function getDefaults() {
        return array(
            'single-switch'          => 0,
            'slide-width'            => 600,
            'slide-height'           => 400,
            'maximum-pane-width'     => 3000,
            'minimum-slide-gap'      => 10,
            'background-color'       => 'dee3e6ff',
            'background'             => '',
            'background-size'        => 'cover',
            'background-fixed'       => 0,
            'animation'              => 'horizontal',
            'animation-duration'     => 800,
            'animation-easing'       => 'easeOutQuad',
            'carousel'               => 1,
            'border-width'           => 0,
            'border-color'           => '3E3E3Eff',
            'border-radius'          => 0,
            'slide-background-color' => 'ffffff',
            'slide-border-radius'    => 0
        );
    }

    protected function renderType($css) {
        if ($this->slider->params->get('animation') === 'horizontal' && $this->slider->params->get('single-switch', 0)) {
            $this->renderTypeSingle($css);
        } else {
            $this->renderTypeMulti($css);
        }
    }

    protected function renderTypeMulti($css) {

        $params = $this->slider->params;

        Js::addStaticGroup(SliderTypeCarousel::getAssetsPath() . '/dist/smartslider-carousel-type-frontend.min.js', 'smartslider-carousel-type-frontend');

        $this->jsDependency[] = 'smartslider-carousel-type-frontend';

        $background = $params->get('background');
        $sliderCSS  = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS = 'background-image: URL(' . ResourceTranslator::toUrl($background) . ');';
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();
        ?>
        <div class="n2-ss-slider-1 n2_ss__touch_element n2-ow">
            <div class="n2-ss-slider-2 n2-ow" style="<?php echo Sanitize::esc_attr($sliderCSS); ?>">
                <div class="n2-ss-slider-3 n2-ow">
                    <?php
                    echo $this->slider->staticHtml;
                    ?>
                    <div class="n2-ss-slider-pane n2-ow">
                        <?php
                        foreach ($this->slider->getSlides() as $i => $slide) {
                            $slide->finalize();

                            echo Html::tag('div', Html::mergeAttributes($slide->attributes, $slide->linkAttributes, array(
                                'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                'style' => $slide->style . $params->get('slide-css')
                            )), $slide->background . $slide->getHTML());
                        }
                        ?>
                    </div>
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


        $this->javaScriptProperties['mainanimation'] = array(
            'type'     => $params->get('animation'),
            'duration' => intval($params->get('animation-duration')),
            'ease'     => $params->get('animation-easing')
        );

        $this->javaScriptProperties['carousel']                      = intval($params->get('carousel'));
        $this->javaScriptProperties['maxPaneWidth']                  = intval($params->get('maximum-pane-width'));
        $this->javaScriptProperties['responsive']['minimumSlideGap'] = intval($params->get('minimum-slide-gap'));

        $sideSpacing = array();

        if ($params->get('side-spacing-desktop-enable', 0)) {
            $sideSpacing['desktop'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-desktop'))), 4, 0);
        } else {
            $verticalSpacing        = intval(max(0, $params->get('height') - $params->get('slide-height')) / 2);
            $sideSpacing['desktop'] = array(
                $verticalSpacing,
                0,
                $verticalSpacing,
                0
            );
        }

        if ($params->get('side-spacing-tablet-enable', 0)) {
            $sideSpacing['tablet'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-tablet'))), 4, 0);
        } else {
            $sideSpacing['tablet'] = $sideSpacing['desktop'];
        }

        if ($params->get('side-spacing-mobile-enable', 0)) {
            $sideSpacing['mobile'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-mobile'))), 4, 0);
        } else {
            $sideSpacing['mobile'] = $sideSpacing['tablet'];
        }

        $this->javaScriptProperties['responsive']['sideSpacing'] = $sideSpacing;

        $this->javaScriptProperties['responsive']['border'] = max(0, intval($params->get('border-width', 0)));

        $this->javaScriptProperties['parallax']['enabled'] = 0;

        $this->style .= $css->getCSS();
    }

    protected function renderTypeSingle($css) {

        $params = $this->slider->params;

        Js::addStaticGroup(SliderTypeCarousel::getAssetsPath() . '/dist/smartslider-carousel-single-type-frontend.min.js', 'smartslider-carousel-single-type-frontend');

        $this->jsDependency[] = 'smartslider-carousel-single-type-frontend';

        $background = $params->get('background');
        $sliderCSS  = $params->get('slider-css');
        if (!empty($background)) {
            $sliderCSS = 'background-image: URL(' . ResourceTranslator::toUrl($background) . ');';
        }

        $this->initParticleJS();

        echo $this->openSliderElement();
        $this->widgets->echoAbove();
        ?>
        <div class="n2-ss-slider-1 n2_ss__touch_element n2-ow">
            <div class="n2-ss-slider-2 n2-ow" style="<?php echo Sanitize::esc_attr($sliderCSS); ?>">
                <div class="n2-ss-slider-3 n2-ow">
                    <?php
                    echo $this->slider->staticHtml;
                    ?>
                    <div class="n2-ss-slider-pane-single n2-ow">
                        <div class="n2-ss-slider-pipeline n2-ow"><?php
                            foreach ($this->slider->getSlides() as $i => $slide) {
                                $slide->finalize();

                                echo Html::tag('div', Html::mergeAttributes($slide->attributes, $slide->linkAttributes, array(
                                    'class' => 'n2-ss-slide ' . $slide->classes . ' n2-ss-canvas n2-ow',
                                    'style' => $slide->style . $params->get('slide-css')
                                )), $slide->background . $slide->getHTML());
                            }
                            ?></div>
                    </div>
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

        $this->javaScriptProperties['mainanimation'] = array(
            'duration' => intval($params->get('animation-duration')),
            'ease'     => $params->get('animation-easing')
        );

        $this->javaScriptProperties['carousel']                      = intval($params->get('carousel'));
        $this->javaScriptProperties['maxPaneWidth']                  = intval($params->get('maximum-pane-width'));
        $this->javaScriptProperties['responsive']['minimumSlideGap'] = intval($params->get('minimum-slide-gap'));
        $this->javaScriptProperties['responsive']['justifySlides']   = intval($params->get('slider-side-spacing', 1));

        $sideSpacing = array();

        if ($params->get('side-spacing-desktop-enable', 0)) {
            $sideSpacing['desktop'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-desktop'))), 4, 0);
        } else {
            $verticalSpacing        = intval(max(0, $params->get('height') - $params->get('slide-height')) / 2);
            $sideSpacing['desktop'] = array(
                $verticalSpacing,
                0,
                $verticalSpacing,
                0
            );
        }

        if ($params->get('side-spacing-tablet-enable', 0)) {
            $sideSpacing['tablet'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-tablet'))), 4, 0);
        } else {
            $sideSpacing['tablet'] = $sideSpacing['desktop'];
        }

        if ($params->get('side-spacing-mobile-enable', 0)) {
            $sideSpacing['mobile'] = array_pad(array_map('intval', explode('|*|', $params->get('side-spacing-mobile'))), 4, 0);
        } else {
            $sideSpacing['mobile'] = $sideSpacing['tablet'];
        }

        $this->javaScriptProperties['responsive']['sideSpacing'] = $sideSpacing;

        $this->javaScriptProperties['responsive']['border'] = max(0, intval($params->get('border-width', 0)));

        $this->style .= $css->getCSS();
    }


    public function getScript() {
        if ($this->slider->params->get('animation') === 'horizontal' && $this->slider->params->get('single-switch', 0)) {
            return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderCarouselSingle('#{$this->slider->elementId}', " . $this->encodeJavaScriptProperties() . ");});";
        } else {
            return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderCarousel('#{$this->slider->elementId}', " . $this->encodeJavaScriptProperties() . ");});";
        }
    }

    /**
     * @param $params Data
     */
    public function limitParams($params) {
        $limitParams = array(
            'widget-bar-enabled'        => 0,
            'widget-fullscreen-enabled' => 0,
            'responsiveLimitSlideWidth' => 0
        );

        if ($params->get('responsive-mode') === 'fullpage') {
            $limitParams['responsive-mode'] = 'auto';
        }

        $params->loadArray($limitParams);
    }
}