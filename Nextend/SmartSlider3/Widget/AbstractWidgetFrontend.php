<?php

namespace Nextend\SmartSlider3\Widget;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Pattern\GetAssetsPathTrait;
use Nextend\SmartSlider3\Slider\Slider;

abstract class AbstractWidgetFrontend {

    use GetAssetsPathTrait;

    /** @var SliderWidget */
    protected $sliderWidget;

    /** @var AbstractWidget */
    protected $widget;

    protected $key;

    /**
     * AbstractWidgetFrontend constructor.
     *
     * @param SliderWidget   $sliderWidget
     * @param AbstractWidget $widget
     */
    public function __construct($sliderWidget, $widget) {
        $this->sliderWidget = $sliderWidget;
        $this->widget       = $widget;

        $this->key = $widget->getKey();
    }

    public function getPositions(&$params) {
        return array();
    }

    public function getDefaults() {
        return $this->widget->getDefaults();
    }

    /**
     * @param Slider $slider
     * @param        $id
     * @param Data   $params
     *
     * @return mixed
     */
    public abstract function render($slider, $id, $params);

    /**
     * @param Data    $params
     * @param string  $key
     * @param integer $showOnMobileDefault
     *
     * @return array
     */
    protected function getDisplayAttributes($params, $key, $showOnMobileDefault = 0) {
        $class = 'n2-ss-widget ';
        if (!$params->get($key . 'display-desktoplandscape', 1)) $class .= 'n2-ss-widget-hide-desktoplandscape ';
    

        if (!$params->get($key . 'display-desktopportrait', 1)) $class .= 'n2-ss-widget-hide-desktopportrait ';
        if (!$params->get($key . 'display-tabletlandscape', 1)) $class .= 'n2-ss-widget-hide-tabletlandscape ';
    

        if (!$params->get($key . 'display-tabletportrait', 1)) $class .= 'n2-ss-widget-hide-tabletportrait ';
        if (!$params->get($key . 'display-mobilelandscape', $showOnMobileDefault)) $class .= 'n2-ss-widget-hide-mobilelandscape ';
    

        if (!$params->get($key . 'display-mobileportrait', $showOnMobileDefault)) $class .= 'n2-ss-widget-hide-mobileportrait ';

        if ($params->get($key . 'display-hover', 0)) $class .= 'n2-ss-widget-display-hover ';

        $attributes = array();

        $excludeSlides = $params->get($key . 'exclude-slides', '');
        if (!empty($excludeSlides)) {
            $attributes['data-exclude-slides'] = $excludeSlides;
        }

        return array(
            $class,
            $attributes
        );
    }

    /**
     * @param Data   $params
     * @param string $key
     *
     * @return array
     */
    protected function getPosition($params, $key) {
        $mode = $params->get($key . 'position-mode', 'simple');
        if ($mode == 'above') {
            return array(
                'margin-bottom:' . $params->get($key . 'position-offset', 0) . 'px;',
                array(
                    'data-position' => 'above'
                )
            );
        } else if ($mode == 'below') {
            return array(
                'margin-top:' . $params->get($key . 'position-offset', 0) . 'px;',
                array(
                    'data-position' => 'below'
                )
            );
        }
        $attributes = array();
        $style      = 'position: absolute;';

        $side     = $params->get($key . 'position-horizontal', 'left');
        $position = $params->get($key . 'position-horizontal-position', 0);
        $unit     = $params->get($key . 'position-horizontal-unit', 'px');

        if (!is_numeric($position)) {
            $attributes['data-ss' . $side] = $position;
        } else {
            $style .= $side . ':' . $position . $unit . ';';
        }

        $side     = $params->get($key . 'position-vertical', 'top');
        $position = $params->get($key . 'position-vertical-position', 0);
        $unit     = $params->get($key . 'position-vertical-unit', 'px');

        if (!is_numeric($position)) {
            $attributes['data-ss' . $side] = $position;
        } else {
            $style .= $side . ':' . $position . $unit . ';';
        }

        return array(
            $style,
            $attributes
        );
    }

    /**
     * @param Data   $params
     * @param string $key
     *
     * @return bool
     */
    protected function isNormalFlow($params, $key) {

        $mode = $params->get($key . 'position-mode', 'simple');

        return ($mode == 'above' || $mode == 'below');
    }

    public static function getOrientationByPosition($mode, $area, $set = 'auto', $default = 'horizontal') {
        if ($mode == 'advanced') {
            if ($set == 'auto') {
                return $default;
            }

            return $set;
        }
        if ($set != 'auto') {
            return $set;
        }
        switch ($area) {
            case '5':
            case '6':
            case '7':
            case '8':
                return 'vertical';
                break;
        }

        return 'horizontal';
    }
}