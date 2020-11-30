<?php


namespace Nextend\SmartSlider3Pro\Widget\FullScreen\FullScreenImage;


use Nextend\Framework\Cast;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class FullScreenImageFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions = array();

        $positions['fullscreen-position'] = array(
            $this->key . 'position-',
            'fullscreen'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        $html = '';

        $toNormalImage = $params->get($this->key . 'tonormal-image');
        $toNormalValue = $params->get($this->key . 'tonormal');
        $toNormalColor = $params->get($this->key . 'tonormal-color');

        if (empty($toNormalImage)) {
            if ($toNormalValue == -1) {
                $toNormal = null;
            } else {
                $toNormal = ResourceTranslator::pathToResource(self::getAssetsPath() . '/tonormal/' . basename($toNormalValue));
            }
        } else {
            $toNormal = $toNormalImage;
        }

        if ($params->get($this->key . 'mirror')) {
            $toFullColor = $toNormalColor;
            if (!empty($toNormalImage)) {
                $toFull = $toNormalImage;
            } else {
                $toFull = ResourceTranslator::pathToResource(self::getAssetsPath() . '/tofull/' . basename($toNormalValue));
            }
        } else {
            $toFull      = $params->get($this->key . 'tofull-image');
            $toFullColor = $params->get($this->key . 'tofull-color');
            if (empty($toFull)) {
                $toFullValue = $params->get($this->key . 'tofull');
                if ($toFull == -1) {
                    $toFull = null;
                } else {
                    $toFull = ResourceTranslator::pathToResource(self::getAssetsPath() . '/tofull/' . basename($toFullValue));
                }
            }
        }


        if ($toNormal && $toFull) {


            $ext = pathinfo($toNormal, PATHINFO_EXTENSION);
            if ($ext == 'svg' && ResourceTranslator::isResource($toNormal)) {
                list($color, $opacity) = Color::colorToSVG($toNormalColor);
                $toNormal = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), Filesystem::readFile(ResourceTranslator::toPath($toNormal))));
            } else {
                $toNormal = ResourceTranslator::toUrl($toNormal);
            }

            $ext = pathinfo($toFull, PATHINFO_EXTENSION);
            if ($ext == 'svg' && ResourceTranslator::isResource($toFull)) {
                list($color, $opacity) = Color::colorToSVG($toFullColor);
                $toFull = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), Filesystem::readFile(ResourceTranslator::toPath($toFull))));
            } else {
                $toFull = ResourceTranslator::toUrl($toFull);
            }

            $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
                "sliderid" => $slider->elementId
            ));
            $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/fullscreen.min.js'));
        

            list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

            $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading');

            $isNormalFlow = $this->isNormalFlow($params, $this->key);
            list($style, $attributes) = $this->getPosition($params, $this->key);


            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetFullScreenImage(this, ' . Cast::floatToString($params->get($this->key . 'responsive-desktop')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-tablet')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-mobile')) . ');');

            $html = Html::tag('div', $displayAttributes + $attributes + array(
                    'class' => $displayClass . $styleClass . 'n2-full-screen-widget n2-ow n2-full-screen-widget-image nextend-fullscreen ' . ($isNormalFlow ? '' : 'n2-ib'),
                    'style' => $style . ($isNormalFlow ? 'margin-left:auto;margin-right:auto;' : '')
                ), Html::image($toNormal, 'Full screen', Html::addExcludeLazyLoadAttributes(array(
                    'class'    => 'n2-full-screen-widget-to-normal n2-ow',
                    'tabindex' => '0'
                ))) . Html::image($toFull, 'Exit full screen', Html::addExcludeLazyLoadAttributes(array(
                    'class'    => 'n2-full-screen-widget-to-full n2-ow',
                    'tabindex' => '0'
                ))));
        }

        return $html;
    }
}