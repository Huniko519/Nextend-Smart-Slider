<?php

namespace Nextend\SmartSlider3Pro\Widget\Bar\BarVertical;

use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class BarVerticalFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions = array();

        $positions['bar-position'] = array(
            $this->key . 'position-',
            'bar'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/bar.min.js'));
    

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key, 1);

        $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'simple');

        $fontTitle       = $slider->addFont($params->get($this->key . 'font-title'), 'simple');
        $fontDescription = $slider->addFont($params->get($this->key . 'font-description'), 'simple');

        list($style, $attributes) = $this->getPosition($params, $this->key);
        $attributes['data-offset'] = $params->get($this->key . 'position-offset');


        $style .= 'text-align: ' . $params->get($this->key . 'align', 'left') . ';';

        $width = $params->get($this->key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
            if (substr($width, -1) == '%') {
                $attributes['data-width-percent'] = substr($width, 0, -1);
            }
        }

        $height = $params->get($this->key . 'height');
        if (is_numeric($height) || substr($height, -1) == '%' || substr($height, -2) == 'px') {
            $style .= 'height:' . $height . ';';
            if (substr($height, -1) == '%') {
                $attributes['data-height-percent'] = substr($height, 0, -1);
            }
        }

        $parameters = array(
            'area'            => intval($params->get($this->key . 'position-area')),
            'animate'         => intval($params->get($this->key . 'animate')),
            'fontTitle'       => $fontTitle,
            'fontDescription' => $fontDescription
        );

        $slider->exposeSlideData['description'] = true;

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetBarVertical(this, ' . json_encode($parameters) . ');');

        return Html::tag("div", $displayAttributes + $attributes + array(
                "class" => $displayClass . "nextend-bar nextend-bar-vertical n2-ow",
                "style" => $style
            ), Html::tag("div", array(
            "class" => $styleClass . ' n2-ow'
        ), Html::tag("div", array('class' => 'n2-ow'), '')));
    }
}