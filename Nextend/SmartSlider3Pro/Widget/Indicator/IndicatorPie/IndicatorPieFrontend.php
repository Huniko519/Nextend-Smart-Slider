<?php


namespace Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorPie;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class IndicatorPieFrontend extends AbstractWidgetFrontend {

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


        $isNormalFlow = $this->isNormalFlow($params, $this->key);

        list($style, $attributes) = $this->getPosition($params, $this->key);

        $track      = Color::colorToSVG($params->get($this->key . 'track'));
        $bar        = Color::colorToSVG($params->get($this->key . 'bar'));
        $parameters = array(
            'backstroke'         => $track[0],
            'backstrokeopacity'  => $track[1],
            'frontstroke'        => $bar[0],
            'frontstrokeopacity' => $bar[1],
            'size'               => intval($params->get($this->key . 'size')),
            'thickness'          => $params->get($this->key . 'thickness') / 100
        );

        $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading');

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetIndicatorPie(this, ' . json_encode($parameters) . ');');

        return Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . $styleClass . " nextend-indicator nextend-indicator-pie n2-ow" . ($isNormalFlow ? ' n2-flex' : ' n2-ib'),
                'style' => $style . ($isNormalFlow ? 'justify-content:center;' : '')
            ));
    }
}