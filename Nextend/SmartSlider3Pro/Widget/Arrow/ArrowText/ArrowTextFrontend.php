<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowText;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ArrowTextFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions = array();

        $positions['previous-position'] = array(
            $this->key . 'previous-position-',
            'previous'
        );

        $positions['next-position'] = array(
            $this->key . 'next-position-',
            'next'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        if ($slider->getSlidesCount() <= 1) {
            return '';
        }
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/arrow.min.js'));
    

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowText(this);');

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));

        $font  = $slider->addFont($params->get($this->key . 'font'), 'hover');
        $style = $slider->addStyle($params->get($this->key . 'style'), 'heading');

        return array(
            'previous' => $this->getHtml($id, $params, 'previous', $displayClass, $displayAttributes, $font, $style),
            'next'     => $this->getHtml($id, $params, 'next', $displayClass, $displayAttributes, $font, $style)
        );
    }

    private function getHtml($id, &$params, $side, $displayClass, $displayAttributes, $font, $styleClass) {

        $isNormalFlow = $this->isNormalFlow($params, $this->key . $side . '-');
        list($style, $attributes) = $this->getPosition($params, $this->key . $side . '-');

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        $html = Html::openTag("div", $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                "class"      => $displayClass . " n2-ow nextend-arrow nextend-arrow-{$side} " . ($isNormalFlow ? '' : 'n2-ib'),
                "style"      => $style . ($isNormalFlow ? 'text-align:center;' : ''),
                'role'       => 'button',
                'aria-label' => $label
            ));


        $html .= Html::tag('div', array(
            "class" => $styleClass . ' ' . $font . ' nextend-arrow-text n2-ow',
            "style" => 'display:inline-block'
        ), $params->get($this->key . $side . '-label'));

        $html .= Html::closeTag("div");

        return $html;
    }
}