<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowGrow;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ArrowGrowFrontend extends AbstractWidgetFrontend {

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
        $return = array();

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/arrow.min.js'));
    

        $previousValue = basename($params->get($this->key . 'previous'));
        if ($previousValue == -1) {
            $previous = false;
        } else {
            $previous = ResourceTranslator::pathToResource(self::getAssetsPath() . '/previous/' . $previousValue);
        }
        $previousColor = $params->get($this->key . 'previous-color');
        if ($params->get($this->key . 'mirror')) {
            if ($previousValue == -1) {
                $next = false;
            } else {
                $next = ResourceTranslator::pathToResource(self::getAssetsPath() . '/next/' . $previousValue);
            }
            $nextColor = $previousColor;
        } else {
            $nextValue = basename($params->get($this->key . 'next'));
            if ($nextValue == -1) {
                $next = false;
            } else {
                $next = ResourceTranslator::pathToResource(self::getAssetsPath() . '/next/' . $nextValue);
            }
            $nextColor = $params->get($this->key . 'next-color');
        }

        $fontClass  = $slider->addFont($params->get($this->key . 'font'), 'hover');
        $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading');

        if ($previous) {
            $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $fontClass, $styleClass, $previousColor);
        }
        if ($next) {
            $return['next'] = $this->getHTML($slider, $id, $params, 'next', $next, $fontClass, $styleClass, $nextColor);
        }

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowGrow(this, ' . $params->get($this->key . 'animation-delay') . ');');

        return $return;
    }

    /**
     * @param Slider $slider
     * @param        $id
     * @param        $params
     * @param        $side
     * @param        $image
     * @param        $fontClass
     * @param        $styleClass
     * @param        $color
     *
     * @return string
     */
    protected function getHTML($slider, $id, &$params, $side, $image, $fontClass, $styleClass, $color) {

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

        list($style, $attributes) = $this->getPosition($params, $this->key . $side . '-');

        $ext = pathinfo($image, PATHINFO_EXTENSION);
        if ($ext == 'svg' && ResourceTranslator::isResource($image)) {

            list($color, $opacity) = Color::colorToSVG($color);
            $image = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), Filesystem::readFile(ResourceTranslator::toPath($image))));
        } else {
            $image = ResourceTranslator::toUrl($image);
        }

        $label = '';
        switch ($side) {
            case 'previous':
                $label = 'Previous slide';
                break;
            case 'next':
                $label = 'Next slide';
                break;
        }

        $isNormalFlow = $this->isNormalFlow($params, $this->key . $side . '-');

        return Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . $styleClass . 'nextend-arrow n2-ow nextend-arrow-grow nextend-arrow-' . $side . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), Html::tag('div', array(
                'class' => $fontClass . ' nextend-arrow-title n2-ow'
            ), '') . Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }
}