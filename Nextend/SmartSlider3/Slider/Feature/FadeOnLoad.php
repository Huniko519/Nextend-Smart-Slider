<?php


namespace Nextend\SmartSlider3\Slider\Feature;


use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Sanitize;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\Slider;

class FadeOnLoad {

    /**
     * @var Slider
     */
    private $slider;

    public $fadeOnLoad = 1;

    public $fadeOnScroll = 0;

    public $playWhenVisible = 1;

    public $playWhenVisibleAt = 0.5;

    public $placeholderColor = 'RGBA(255,255,255,0)';

    public $customSpinner = '';

    public $placeholderBackground = '';

    public $minimumHeight = 0;

    public function __construct($slider) {

        $this->slider = $slider;

        $this->fadeOnLoad            = intval($slider->params->get('fadeOnLoad', 1));
        $this->fadeOnScroll          = intval($slider->params->get('fadeOnScroll', 0));
        $this->playWhenVisible       = intval($slider->params->get('playWhenVisible', 1));
        $this->playWhenVisibleAt     = max(0, min(100, intval($slider->params->get('playWhenVisibleAt', 50)))) / 100;
        $this->placeholderColor      = Color::colorToRGBA($this->slider->params->get('placeholder-color', 'FFFFFF00'));
        $this->placeholderBackground = ResourceTranslator::toUrl($this->slider->params->get('placeholder-background-image', ''));
        $this->customSpinner         = !!$this->slider->params->get('custom-placeholder', 0) ? ResourceTranslator::toUrl($this->slider->params->get('custom-spinner', '')) : '';
        $this->minimumHeight         = intval($this->slider->params->get('responsiveSliderHeightMin', 0));

        if (!empty($this->fadeOnScroll) && $this->fadeOnScroll) {
            $this->fadeOnLoad   = 1;
            $this->fadeOnScroll = 1;
        } else {
            $this->fadeOnScroll = 0;
        }
    }

    public function forceFadeOnLoad() {
        if (!$this->fadeOnScroll && !$this->fadeOnLoad) {
            $this->fadeOnLoad = 1;
        }
    }

    public function getSliderClass() {
        if ($this->fadeOnLoad) {
            return 'n2-ss-load-fade ';
        }

        return '';
    }

    public function renderPlaceholder($sizes) {

        if (!$this->slider->isAdmin && $this->fadeOnLoad && ($this->slider->features->responsive->scaleDown || $this->slider->features->responsive->scaleUp)) {

            if ($sizes['width'] + $sizes['marginHorizontal'] > 0 && $sizes['height'] > 0) {

                if (!empty($this->customSpinner)) {
                    $style = "background-image:url('" . $this->customSpinner . "'); background-size:cover; ";
                } else if (!empty($this->placeholderBackground)) {
                    $style = "background-image:url('" . $this->placeholderBackground . "'); background-size:cover; ";
                } else {
                    $style = '';
                }

                return Html::tag("div", array(
                    "id"     => $this->slider->elementId . "-placeholder",
                    "encode" => false,
                    "style"  => 'min-height:' . $this->minimumHeight . 'px;position: relative;z-index:2;background-color:RGBA(0,0,0,0);' . Sanitize::esc_attr($style) . ' background-color:' . $this->placeholderColor . ';'
                ), $this->makeImage($sizes));
            } else {
                $this->slider->addCSS("#{$this->slider->elementId} .n2-ss-load-fade{position: relative !important;}");
            }
        } else {
            $this->slider->addCSS("#{$this->slider->elementId}.n2-ss-load-fade{position: relative !important;}");
        }

        return '';
    }

    public function makeJavaScriptProperties(&$properties) {
        $properties['load']              = array(
            'fade'   => $this->fadeOnLoad,
            'scroll' => ($this->fadeOnScroll & !$this->slider->isAdmin)
        );
        $properties['playWhenVisible']   = $this->playWhenVisible;
        $properties['playWhenVisibleAt'] = $this->playWhenVisibleAt;
    }


    private function makeImage($sizes) {
        $html = Html::image("data:image/svg+xml;base64," . $this->transparentImage($sizes['width'] + $sizes['marginHorizontal'], $sizes['height']), 'Slider', Html::addExcludeLazyLoadAttributes(array(
            'style' => 'width: 100%; max-width:' . ($this->slider->features->responsive->maximumSlideWidth + $sizes['marginHorizontal']) . 'px; display: block;opacity:0;margin:0px;',
            'class' => 'n2-ow'
        )));

        if ($sizes['marginVertical'] > 0) {
            $html .= Html::image("data:image/svg+xml;base64," . $this->transparentImage($sizes['width'] + $sizes['marginHorizontal'], $sizes['marginVertical']), 'Slider', Html::addExcludeLazyLoadAttributes(array(
                'style' => 'width: 100%;margin:0px;',
                'class' => 'n2-ow'
            )));
        }

        return $html;
    }

    private function transparentImage($width, $height) {

        return Base64::encode('<svg xmlns="http://www.w3.org/2000/svg" width="' . $width . '" height="' . $height . '" ></svg>');
    }

    private static function gcd($a, $b) {
        return ($a % $b) ? self::gcd($b, $a % $b) : $b;
    }
}