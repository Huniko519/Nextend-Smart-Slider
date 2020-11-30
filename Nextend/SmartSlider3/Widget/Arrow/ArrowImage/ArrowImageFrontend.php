<?php

namespace Nextend\SmartSlider3\Widget\Arrow\ArrowImage;

use Nextend\Framework\Cast;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ArrowImageFrontend extends AbstractWidgetFrontend {


    public function getPositions(&$params) {
        $positions = array();

        if ($this->isRenderable('previous', $params)) {
            $positions['previous-position'] = array(
                $this->key . 'previous-position-',
                'previous'
            );
        }

        if ($this->isRenderable('next', $params)) {
            $positions['next-position'] = array(
                $this->key . 'next-position-',
                'next'
            );
        }

        return $positions;
    }

    private function isRenderable($side, &$params) {
        $arrow = $params->get($this->key . $side . '-image');
        if (empty($arrow)) {
            $arrow = $params->get($this->key . $side);
            if ($arrow == -1) {
                $arrow = null;
            }
        }

        return !!$arrow;
    }

    public function render($slider, $id, $params) {
        if ($slider->getSlidesCount() <= 1) {
            return '';
        }
        $return = array();

        $previousImage      = $params->get($this->key . 'previous-image');
        $previousValue      = $params->get($this->key . 'previous');
        $previousColor      = $params->get($this->key . 'previous-color');
        $previousHover      = $params->get($this->key . 'previous-hover');
        $previousHoverColor = $params->get($this->key . 'previous-hover-color');

        if (empty($previousImage)) {

            if ($previousValue == -1) {
                $previous = false;
            } else {
                $previous = ResourceTranslator::pathToResource(self::getAssetsPath() . '/previous/' . basename($previousValue));
            }
        } else {
            $previous = $previousImage;
        }

        if ($params->get($this->key . 'mirror')) {
            $nextColor      = $previousColor;
            $nextHover      = $previousHover;
            $nextHoverColor = $previousHoverColor;

            if (empty($previousImage)) {
                if ($previousValue == -1) {
                    $next = false;
                } else {
                    $next = ResourceTranslator::pathToResource(self::getAssetsPath() . '/next/' . basename($previousValue));
                }
            } else {
                $next = $previousImage;
                $slider->addCSS('#' . $id . '-arrow-next' . '{transform: rotate(180deg);}');
            }
        } else {
            $next           = $params->get($this->key . 'next-image');
            $nextColor      = $params->get($this->key . 'next-color');
            $nextHover      = $params->get($this->key . 'next-hover');
            $nextHoverColor = $params->get($this->key . 'next-hover-color');

            if (empty($next)) {
                $nextValue = $params->get($this->key . 'next');
                if ($nextValue == -1) {
                    $next = false;
                } else {
                    $next = ResourceTranslator::pathToResource(self::getAssetsPath() . '/next/' . basename($nextValue));
                }
            }
        }
        if ($previous || $next) {

            $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
                "sliderid" => $slider->elementId
            ));
            $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/arrow.min.js'));
        

            list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

            $animation = $params->get($this->key . 'animation');

            if ($animation == 'none' || $animation == 'fade') {
                $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading');
            } else {
                $styleClass = $slider->addStyle($params->get($this->key . 'style'), 'heading-active');
            }

            if ($previous) {
                $return['previous'] = $this->getHTML($id, $params, $animation, 'previous', $previous, $displayClass, $displayAttributes, $styleClass, $previousColor, $previousHover, $previousHoverColor);
            }

            if ($next) {
                $return['next'] = $this->getHTML($id, $params, $animation, 'next', $next, $displayClass, $displayAttributes, $styleClass, $nextColor, $nextHover, $nextHoverColor);
            }

            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetArrowImage(this, ' . Cast::floatToString($params->get($this->key . 'responsive-desktop')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-tablet')) . ', ' . Cast::floatToString($params->get($this->key . 'responsive-mobile')) . ');');
        }

        return $return;
    }

    /**
     * @param string $id
     * @param Data   $params
     * @param string $animation
     * @param string $side
     * @param string $image
     * @param string $displayClass
     * @param string $displayAttributes
     * @param string $styleClass
     * @param string $color
     * @param int    $hover
     * @param string $hoverColor
     *
     * @return string
     */
    private function getHTML($id, $params, $animation, $side, $image, $displayClass, $displayAttributes, $styleClass, $color = 'ffffffcc', $hover = 0, $hoverColor = 'ffffffcc') {

        list($style, $attributes) = $this->getPosition($params, $this->key . $side . '-');

        $imageHover = null;

        $ext = pathinfo($image, PATHINFO_EXTENSION);

        /**
         * We can not colorize SVGs when base64 disabled.
         */
        if ($ext == 'svg' && ResourceTranslator::isResource($image) && $params->get($this->key . 'base64', 1)) {

            list($color, $opacity) = Color::colorToSVG($color);
            $content = Filesystem::readFile(ResourceTranslator::toPath($image));
            $image   = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                    'fill="#FFF"',
                    'opacity="1"'
                ), array(
                    'fill="#' . $color . '"',
                    'opacity="' . $opacity . '"'
                ), $content));

            if ($hover) {
                list($color, $opacity) = Color::colorToSVG($hoverColor);
                $imageHover = 'data:image/svg+xml;base64,' . Base64::encode(str_replace(array(
                        'fill="#FFF"',
                        'opacity="1"'
                    ), array(
                        'fill="#' . $color . '"',
                        'opacity="' . $opacity . '"'
                    ), $content));
            }
        } else {
            $image = ResourceTranslator::toUrl($image);
        }

        $alt = $params->get($this->key . $side . '-alt', $side . ' arrow');

        if ($imageHover === null) {
            $image = Html::image($image, $alt, Html::addExcludeLazyLoadAttributes(array(
                'class' => 'n2-ow'
            )));
        } else {
            $image = Html::image($image, $alt, Html::addExcludeLazyLoadAttributes(array(
                    'class' => 'n2-arrow-normal-img n2-ow'
                ))) . Html::image($imageHover, $alt, Html::addExcludeLazyLoadAttributes(array(
                    'class' => 'n2-arrow-hover-img n2-ow'
                )));
        }

        $isNormalFlow = $this->isNormalFlow($params, $this->key . $side . '-');

        if ($animation == 'none' || $animation == 'fade') {
            return Html::tag('div', $displayAttributes + $attributes + array(
                    'id'         => $id . '-arrow-' . $side,
                    'class'      => $displayClass . $styleClass . 'nextend-arrow n2-ow nextend-arrow-' . $side . '  nextend-arrow-animated-' . $animation . ($isNormalFlow ? '' : ' n2-ib'),
                    'style'      => $style,
                    'role'       => 'button',
                    'aria-label' => $alt,
                    'tabindex'   => '0'
                ), $image);
        }


        return Html::tag('div', $displayAttributes + $attributes + array(
                'id'         => $id . '-arrow-' . $side,
                'class'      => $displayClass . 'nextend-arrow nextend-arrow-animated n2-ow nextend-arrow-animated-' . $animation . ' nextend-arrow-' . $side . ($isNormalFlow ? '' : ' n2-ib'),
                'style'      => $style,
                'role'       => 'button',
                'aria-label' => $alt,
                'tabindex'   => '0'
            ), Html::tag('div', array(
                'class' => $styleClass . ' n2-resize'
            ), $image) . Html::tag('div', array(
                'class' => $styleClass . ' n2-active' . ' n2-resize'
            ), $image));
    }
}