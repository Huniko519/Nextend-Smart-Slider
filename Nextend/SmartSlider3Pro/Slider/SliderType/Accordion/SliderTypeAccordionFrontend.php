<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Accordion;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;

class SliderTypeAccordionFrontend extends AbstractSliderTypeFrontend {

    public function getDefaults() {
        return array(
            'orientation'         => 'horizontal',
            'carousel'            => 1,
            'outer-border'        => 6,
            'outer-border-color'  => '3E3E3Eff',
            'inner-border'        => 6,
            'inner-border-color'  => '222222ff',
            'border-radius'       => 6,
            'tab-normal-color'    => '3E3E3E',
            'tab-active-color'    => '87B801',
            'slide-margin'        => 2,
            'title-size'          => 30,
            'title-margin'        => 10,
            'title-border-radius' => 2,
            'title-font'          => '{"data":[{"extra":"text-transform: uppercase;","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{"extra":""}]}',
            'animation-duration'  => 1000,
            'slider-outer-css'    => '',
            'slider-inner-css'    => 'box-shadow: 0 1px 3px 1px RGBA(0, 0, 0, .3) inset;',
            'slider-title-css'    => 'box-shadow: 0 0 0 1px RGBA(255, 255, 255, .05) inset, 0 0 2px 1px RGBA(0, 0, 0, .3);',
            'animation-easing'    => 'easeOutQuad'
        );
    }

    protected function renderType($css) {

        $params = $this->slider->params;

        $orientation = $params->get('orientation');

        Js::addStaticGroup(SliderTypeAccordion::getAssetsPath() . '/dist/smartslider-accordion-type-frontend.min.js', 'smartslider-accordion-type-frontend');

        $this->jsDependency[] = 'smartslider-accordion-type-frontend';

        echo $this->openSliderElement();
        $this->widgets->echoAbove();

        echo Html::openTag('div', array(
            'class' => 'n2-ss-slider-1 n2_ss__touch_element n2-ow',
        ));

        echo Html::openTag('div', array(
            'class' => 'n2-ss-slider-2 n2-ow',
            'style' => $params->get('slider-outer-css')
        ));

        echo Html::openTag('div', array(
            'class' => 'n2-ss-slider-3 n2-ow',
            'style' => $params->get('slider-inner-css')
        ));

        echo $this->slider->staticHtml;

        foreach ($this->slider->getSlides() as $i => $slide) {
            $slide->finalize();

            echo Html::openTag('div', $slide->attributes + array(
                    'class' => 'n2-ss-slide n2-ow ' . $slide->classes
                ));
            ?>
            <?php
            $font = $this->slider->addFont($params->get('title-font'), 'accordionslidetitle');

            echo Html::openTag('div', array(
                'class' => 'n2-accordion-title n2-ow-all',
                'style' => $params->get('slider-title-css')
            ));
            ?>
            <div class="n2-accordion-title-inner <?php echo $font; ?>">
                <?php
                if ($orientation == 'horizontal') {
                    ?>
                    <div class="n2-accordion-title-rotate-90">
                        <?php echo $slide->getTitle(); ?>
                    </div>
                    <?php
                } else {
                    echo $slide->getTitle();
                }
                ?>
            </div>
            <?php echo Html::closeTag('div'); ?>

            <?php
            echo Html::openTag('div', Html::mergeAttributes(array(
                'class' => 'n2-accordion-slide n2-ow',
                'style' => $slide->style
            ), $slide->linkAttributes));
            ?>
            <?php
            echo Html::tag('div', array(
                'class' => 'n2-ss-canvas n2-ow',
            ), $slide->background . $slide->getHTML());

            echo HTML::closeTag('div');
            echo HTML::closeTag('div');
        }

        echo Html::closeTag('div');
        echo Html::closeTag('div');
        $this->widgets->echoRemainder();
        echo Html::closeTag('div');

        $this->widgets->echoBelow();
        echo $this->closeSliderElement();

        $this->javaScriptProperties['carousel']      = intval($params->get('carousel'));
        $this->javaScriptProperties['orientation']   = $params->get('orientation');
        $this->javaScriptProperties['mainanimation'] = array(
            'duration' => intval($params->get('animation-duration')),
            'ease'     => $params->get('animation-easing')
        );

        $this->style .= $css->getCSS();
    }

    public function getScript() {
        return "N2R(" . json_encode($this->jsDependency) . ",function(){new N2Classes.SmartSliderAccordion('#{$this->slider->elementId}', " . $this->encodeJavaScriptProperties() . ");});";
    }

    protected function getSliderClasses() {
        return parent::getSliderClasses() . 'n2-accordion-' . $this->slider->params->get('orientation', 'horizontal');
    }

    /**
     * @param $params Data
     */
    public function limitParams($params) {
        $limitParams = array(
            'widget-bar-enabled'        => 0,
            'slide-background-parallax' => 0,
            'widget-fullscreen-enabled' => 0,
            'responsiveLimitSlideWidth' => 0
        );

        if ($params->get('responsive-mode') === 'fullpage') {
            $limitParams['responsive-mode'] = 'auto';
        }

        $params->loadArray($limitParams);
    }
}