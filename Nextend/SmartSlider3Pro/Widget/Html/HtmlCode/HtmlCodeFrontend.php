<?php

namespace Nextend\SmartSlider3Pro\Widget\Html\HtmlCode;

use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class HtmlCodeFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions                  = array();
        $positions['html-position'] = array(
            $this->key . 'position-',
            'html'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/html.min.js'));
    

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetHTML(this);');

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key, 1);

        list($style, $attributes) = $this->getPosition($params, $this->key);

        return Html::tag('div', $displayAttributes + $attributes + array(
                "class" => "n2-widget-html nextend-widget-html n2-notow {$displayClass}",
                "style" => "{$style}z-index: 10",
            ), $params->get($this->key . 'code'));

    }
}