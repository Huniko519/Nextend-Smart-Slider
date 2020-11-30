<?php


namespace Nextend\SmartSlider3\Widget\Bar\BarHorizontal;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class BarHorizontalFrontend extends AbstractWidgetFrontend {

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

        $style .= 'text-align: ' . $params->get($this->key . 'align') . ';';

        $width = $params->get($this->key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        }

        $innerStyle = '';
        if (!$params->get($this->key . 'full-width')) {
            $innerStyle = 'display: inline-block;';
        }

        $showTitle = intval($params->get($this->key . 'show-title'));

        $showDescription = intval($params->get($this->key . 'show-description'));
        if ($showDescription) {
            $slider->exposeSlideData['description'] = true;
        }

        $parameters = array(
            'area'            => intval($params->get($this->key . 'position-area')),
            'animate'         => intval($params->get($this->key . 'animate')),
            'showTitle'       => $showTitle,
            'fontTitle'       => $fontTitle,
            'slideCount'      => intval($params->get($this->key . 'slide-count', 0)),
            'showDescription' => $showDescription,
            'fontDescription' => $fontDescription,
            'separator'       => $params->get($this->key . 'separator')
        );

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetBarHorizontal(this, ' . json_encode($parameters) . ');');

        return Html::tag("div", $displayAttributes + $attributes + array(
                "class" => $displayClass . "nextend-bar nextend-bar-horizontal n2-ow",
                "style" => $style
            ), Html::tag("div", array(
            "class" => $styleClass . ' n2-ow',
            "style" => $innerStyle
        ), ''));
    }
}