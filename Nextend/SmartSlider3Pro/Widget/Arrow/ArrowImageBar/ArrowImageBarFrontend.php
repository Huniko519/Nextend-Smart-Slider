<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowImageBar;


use Nextend\Framework\Data\Data;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ArrowImageBarFrontend extends AbstractWidgetFrontend {


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

        if ($previous) {
            $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $previousColor);
        }
        if ($next) {
            $return['next'] = $this->getHTML($slider, $id, $params, 'next', $next, $nextColor);
        }

        $slider->exposeSlideData['thumbnail'] = true;
        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowImageBar(this);');

        return $return;
    }

    /**
     * @param Slider        $slider
     * @param               $id
     * @param Data          $params
     * @param               $side
     *
     * @return string
     */
    private function getHTML($slider, $id, $params, $side, $image, $color) {

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

        $style .= 'width: ' . intval($params->get($this->key . 'width')) . 'px';

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
                'class'      => $displayClass . 'nextend-arrow nextend-arrow-imagebar n2-ow nextend-arrow-' . $side . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), Html::tag('div', array(
                'class' => 'nextend-arrow-image n2-ow'
            ), '') . Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }
}