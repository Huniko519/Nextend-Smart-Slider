<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Carousel;


use Nextend\Framework\Parser\Color;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeCss;

class SliderTypeCarouselCss extends AbstractSliderTypeCss {

    public function __construct($slider) {
        parent::__construct($slider);

        if ($this->slider->params->get('animation') === 'horizontal' && $this->slider->params->get('single-switch', 0)) {
            $this->constructCarouselSingle();
        } else {
            $this->constructCarouselMulti();
        }
    }

    private function constructCarouselMulti() {

        $params = $this->slider->params;

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);


        $backgroundColor                 = $params->get('background-color');
        $rgba                            = Color::hex2rgba($backgroundColor);
        $this->context['backgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $this->context['backgroundSize']       = $params->getIfEmpty('background-size', 'inherit');
        $this->context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';


        $backgroundColor                      = $params->get('slide-background-color');
        $rgba                                 = Color::hex2rgba($backgroundColor);
        $this->context['slideBackgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $this->context['slideBorderRadius'] = $params->get('slide-border-radius') . 'px';

        $borderWidth                   = max(0, $params->get('border-width', 0));
        $backgroundColor               = $params->get('border-color');
        $this->context['borderRadius'] = $params->get('border-radius') . 'px';


        $this->context['border'] = $borderWidth . 'px';

        $rgba                        = Color::hex2rgba($backgroundColor);
        $this->context['borderrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $width                         = $width - $borderWidth * 2;
        $height                        = $height - $borderWidth * 2;
        $this->context['inner1height'] = $height . 'px';

        $slideBorderWidth                  = max(0, $params->get('slide-border-width', 0));
        $this->context['slideborderwidth'] = $slideBorderWidth . 'px';

        $rgba = Color::hex2rgba($params->get('slide-border-color'));
        if (!empty($rgba)) {
            $this->context['slidebordercolor'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        }

        $this->context['slideouterwidth']  = min($width, max(50, intval($params->get('slide-width')))) . 'px';
        $this->context['slideouterheight'] = min($height, max(50, intval($params->get('slide-height')))) . 'px';

        $this->context['canvaswidth']  = min($width, max(50, intval($params->get('slide-width')))) - 2 * $slideBorderWidth . 'px';
        $this->context['canvasheight'] = min($height, max(50, intval($params->get('slide-height')))) - 2 * $slideBorderWidth . 'px';

        $this->initSizes();

        $this->slider->addLess(SliderTypeCarousel::getAssetsPath() . '/Multi/style.n2less', $this->context);
    }

    private function constructCarouselSingle() {

        $params = $this->slider->params;

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);


        $backgroundColor                 = $params->get('background-color');
        $rgba                            = Color::hex2rgba($backgroundColor);
        $this->context['backgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $this->context['backgroundSize']       = $params->getIfEmpty('background-size', 'inherit');
        $this->context['backgroundAttachment'] = $params->get('background-fixed') ? 'fixed' : 'scroll';


        $backgroundColor                      = $params->get('slide-background-color');
        $rgba                                 = Color::hex2rgba($backgroundColor);
        $this->context['slideBackgroundrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $this->context['slideBorderRadius'] = $params->get('slide-border-radius') . 'px';

        $borderWidth                   = max(0, $params->get('border-width', 0));
        $backgroundColor               = $params->get('border-color');
        $this->context['borderRadius'] = $params->get('border-radius') . 'px';


        $this->context['border'] = $borderWidth . 'px';

        $rgba                        = Color::hex2rgba($backgroundColor);
        $this->context['borderrgba'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $width                         = $width - $borderWidth * 2;
        $height                        = $height - $borderWidth * 2;
        $this->context['inner1height'] = $height . 'px';

        $slideBorderWidth                  = max(0, $params->get('slide-border-width', 0));
        $this->context['slideborderwidth'] = $slideBorderWidth . 'px';

        $rgba = Color::hex2rgba($params->get('slide-border-color'));
        if (!empty($rgba)) {
            $this->context['slidebordercolor'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';
        }

        $this->context['slideouterwidth']  = min($width, max(50, intval($params->get('slide-width')))) . 'px';
        $this->context['slideouterheight'] = min($height, max(50, intval($params->get('slide-height')))) . 'px';

        $this->context['canvaswidth']  = min($width, max(50, intval($params->get('slide-width')))) - 2 * $slideBorderWidth . 'px';
        $this->context['canvasheight'] = min($height, max(50, intval($params->get('slide-height')))) - 2 * $slideBorderWidth . 'px';

        $this->initSizes();

        $this->slider->addLess(SliderTypeCarousel::getAssetsPath() . '/Single/style.n2less', $this->context);
    }
}