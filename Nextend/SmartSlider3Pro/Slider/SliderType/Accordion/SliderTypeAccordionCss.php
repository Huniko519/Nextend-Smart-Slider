<?php


namespace Nextend\SmartSlider3Pro\Slider\SliderType\Accordion;


use Nextend\Framework\Parser\Color;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeCss;

class SliderTypeAccordionCss extends AbstractSliderTypeCss {

    public function __construct($slider) {
        parent::__construct($slider);
        $params = $this->slider->params;

        $orientation = $params->get('orientation');

        $width  = intval($this->context['width']);
        $height = intval($this->context['height']);

        $this->context['borderRadius'] = intval($params->get('border-radius')) . 'px';

        $outerSpacing                  = $params->get('outer-border');
        $this->context['outerSpacing'] = $outerSpacing . 'px';

        $borderOuterColor            = $params->get('outer-border-color');
        $rgba                        = Color::hex2rgba($borderOuterColor);
        $this->context['outerColor'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $innerSpacing                  = $params->get('inner-border');
        $this->context['innerSpacing'] = $innerSpacing . 'px';

        $borderInnerColor            = $params->get('inner-border-color');
        $rgba                        = Color::hex2rgba($borderInnerColor);
        $this->context['innerColor'] = 'RGBA(' . $rgba[0] . ',' . $rgba[1] . ',' . $rgba[2] . ',' . round($rgba[3] / 127, 2) . ')';

        $orientationMargin             = intval($params->get('title-margin'));
        $this->context['titleSpacing'] = $orientationMargin . 'px';

        $this->context['titleColor']       = '#' . $params->get('tab-normal-color');
        $this->context['titleColorActive'] = '#' . $params->get('tab-active-color');

        $slideMargin = max(0, $params->get('slide-margin'));

        $this->context['slideSpacing'] = $slideMargin . 'px';


        $title      = max(10, $params->get('title-size'));
        $titleSizes = $title * $this->context['count'];

        $this->context['titleBorderRadius'] = max(0, intval($params->get('title-border-radius'))) . 'px';


        $width  = $width - 2 * $outerSpacing - 2 * $innerSpacing;
        $height = $height - 2 * $outerSpacing - 2 * $innerSpacing;

        switch ($orientation) {
            case 'vertical':
                $width  = $width - 2 * $slideMargin;
                $height = $height - 2 * $this->context['count'] * $slideMargin;

                $this->context['titleheight']  = $title . "px";
                $this->context['canvaswidth']  = $width . "px";
                $this->context['canvasheight'] = $height - $titleSizes . "px";
                break;
            default:
                $width  = $width - 2 * ($this->context['count']) * $slideMargin;
                $height = $height - 2 * $slideMargin;

                $this->context['titlewidth']   = $title . "px";
                $this->context['canvaswidth']  = $width - $titleSizes . "px";
                $this->context['canvasheight'] = $height . "px";
        }

        $this->initSizes();

        if ($orientation == 'vertical') {
            $this->slider->addLess(SliderTypeAccordion::getAssetsPath() . '/vertical/vertical.n2less', $this->context);
        } else {
            $this->slider->addLess(SliderTypeAccordion::getAssetsPath() . '/horizontal/horizontal.n2less', $this->context);
        }
    }
}