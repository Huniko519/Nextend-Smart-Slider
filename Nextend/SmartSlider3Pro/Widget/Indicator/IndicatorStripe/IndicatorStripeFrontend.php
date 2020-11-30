<?php


namespace Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorStripe;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class IndicatorStripeFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions                       = array();
        $positions['indicator-position'] = array(
            $this->key . 'position-',
            'indicator'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        if (!$params->get('autoplay', 0)) {
            return '';
        }

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/indicator.min.js'));
    

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

        $trackRGBA = Color::colorToRGBA($params->get($this->key . 'track'));
        $barRGBA   = Color::colorToRGBA($params->get($this->key . 'bar'));

        list($style, $attributes) = $this->getPosition($params, $this->key);
        $attributes['data-offset'] = $params->get($this->key . 'position-offset', 0);


        $width = $params->get($this->key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        }

        $height = intval($params->get($this->key . 'height'));

        $parameters = array(
            'area' => intval($params->get($this->key . 'position-area'))
        );

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetIndicatorStripe(this, ' . json_encode($parameters) . ');');

        return Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-indicator nextend-indicator-stripe n2-ow",
                'style' => 'background-color:' . $trackRGBA . ';' . $style
            ), Html::tag('div', array(
            'class' => "nextend-indicator-track  n2-ow",
            'style' => 'height: ' . $height . 'px;background-color:' . $barRGBA . ';'
        ), ''));
    }
}