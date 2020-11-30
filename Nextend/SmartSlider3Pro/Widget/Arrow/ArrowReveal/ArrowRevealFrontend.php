<?php


namespace Nextend\SmartSlider3Pro\Widget\Arrow\ArrowReveal;


use Nextend\Framework\Data\Data;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ArrowRevealFrontend extends AbstractWidgetFrontend {

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

        $RGBA      = Color::colorToRGBA($params->get($this->key . 'background'));
        $titleRGBA = Color::colorToRGBA($params->get($this->key . 'title-background'));


        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid"            => $slider->elementId,
            "arrowBackgroundRGBA" => $RGBA,
            "titleBackgroundRGBA" => $titleRGBA
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

        $fontClass = $slider->addFont($params->get($this->key . 'title-font'), 'simple');

        $animation      = $params->get($this->key . 'animation');
        $animationClass = ' n2-ss-arrow-animation-' . $animation;

        if ($previous) {
            $return['previous'] = $this->getHTML($slider, $id, $params, 'previous', $previous, $fontClass, $animationClass, $previousColor);
        }
        if ($next) {
            $return['next'] = $this->getHTML($slider, $id, $params, 'next', $next, $fontClass, $animationClass, $nextColor);
        }

        $slider->exposeSlideData['thumbnail'] = true;

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowReveal(this,"' . $animation . '");');

        return $return;
    }

    /**
     * @param Slider        $slider
     * @param               $id
     * @param Data          $params
     * @param               $side
     * @param               $image
     * @param               $fontClass
     * @param               $animationClass
     *
     * @return string
     */
    private function getHTML($slider, $id, $params, $side, $image, $fontClass, $animationClass, $color) {

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
                'class'      => $displayClass . 'nextend-arrow n2-ow nextend-arrow-reveal nextend-arrow-' . $side . $animationClass . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $label,
                'tabindex'   => '0'
            ), Html::tag('div', array(
                'class' => ' nextend-arrow-image n2-ow'
            ), $params->get($this->key . 'title-show') ? Html::tag('div', array(
                'class' => $fontClass . ' nextend-arrow-title n2-ow'
            ), '') : '') . Html::tag('div', array(
                'class' => 'nextend-arrow-arrow n2-ow',
                'style' => 'background-image: URL(' . $image . ');'
            ), ''));
    }
}