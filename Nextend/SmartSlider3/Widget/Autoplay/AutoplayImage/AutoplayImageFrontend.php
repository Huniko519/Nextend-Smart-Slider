<?php

namespace Nextend\SmartSlider3\Widget\Autoplay\AutoplayImage;


use Nextend\Framework\Cast;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class AutoplayImageFrontend extends AbstractWidgetFrontend {


    public function getPositions(&$params) {
        $positions = array();

        $positions['autoplay-position'] = array(
            $this->key . 'position-',
            'autoplay'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        if (!$params->get('autoplay', 0)) {
            return '';
        }

        $html = '';

        $playImage = $params->get($this->key . 'play-image');
        $playValue = $params->get($this->key . 'play');
        $playColor = $params->get($this->key . 'play-color');

        if (empty($playImage)) {

            if ($playValue == -1) {
                $play = null;
            } else {
                $play = ResourceTranslator::pathToResource(self::getAssetsPath() . '/play/' . basename($playValue));
            }
        } else {
            $play = $playImage;
        }

        if ($params->get($this->key . 'mirror')) {
            $pauseColor = $playColor;

            if (!empty($playImage)) {
                $pause = $playImage;
            } else {
                $pause = ResourceTranslator::pathToResource(self::getAssetsPath() . '/pause/' . basename($playValue));
            }
        } else {
            $pause      = $params->get($this->key . 'pause-image');
            $pauseColor = $params->get($this->key . 'pause-color');

            if (empty($pause)) {
                $pauseValue = $params->get($this->key . 'pause');
                if ($pauseValue == -1) {
                    $pause = null;
                } else {
                    $pause = ResourceTranslator::pathToResource(self::getAssetsPath() . '/pause/' . basename($pauseValue));
                }
            }
        }

        $ext = pathinfo($play, PATHINFO_EXTENSION);
        if ($ext == 'svg' && ResourceTranslator::isResource($play)) {
            list($color, $opacity) = Color::colorToSVG($playColor);
            $play = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), Filesystem::readFile(ResourceTranslator::toPath($play))));
        } else {
            $play = ResourceTranslator::toUrl($play);
        }

        $ext = pathinfo($pause, PATHINFO_EXTENSION);
        if ($ext == 'svg' && ResourceTranslator::isResource($pause)) {
            list($color, $opacity) = Color::colorToSVG($pauseColor);
            $pause = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), Filesystem::readFile(ResourceTranslator::toPath($pause))));
        } else {
            $pause = ResourceTranslator::toUrl($pause);
        }

        if ($play && $pause) {

            $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
                "sliderid" => $slider->elementId
            ));
            $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/autoplay.min.js'));
        

            list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key, 1);

            $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading');


            $isNormalFlow = $this->isNormalFlow($params, $this->key);
            list($style, $attributes) = $this->getPosition($params, $this->key);


            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetAutoplayImage(this, ' . Cast::floatToString($params->get($this->key . 'responsive-desktop')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-tablet')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-mobile')) . ');');

            $html = Html::tag('div', $displayAttributes + $attributes + array(
                    'class'      => $displayClass . $styleClass . 'nextend-autoplay n2-ow nextend-autoplay-image' . ($isNormalFlow ? '' : ' n2-ib'),
                    'style'      => $style,
                    'role'       => 'button',
                    'aria-label' => n2_('Pause autoplay'),
                    'tabindex'   => '0'
                ), Html::image($play, 'Play', HTML::addExcludeLazyLoadAttributes(array(
                    'class' => 'nextend-autoplay-play n2-ow'
                ))) . Html::image($pause, 'Pause', HTML::addExcludeLazyLoadAttributes(array(
                    'class' => 'nextend-autoplay-pause n2-ow'
                ))));
        }

        return $html;
    }
}