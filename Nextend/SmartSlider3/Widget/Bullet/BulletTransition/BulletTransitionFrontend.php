<?php


namespace Nextend\SmartSlider3\Widget\Bullet\BulletTransition;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\Bullet\AbstractBulletFrontend;

class BulletTransitionFrontend extends AbstractBulletFrontend {

    public function getPositions(&$params) {
        $positions                    = array();
        $positions['bullet-position'] = array(
            $this->key . 'position-',
            'bullet'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        if ($slider->getSlidesCount() <= 1) {
            return '';
        }

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(Filesystem::readFile($this->getCommonAssetsPath() . '/dist/bullet.min.js'));
    

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key, 1);

        $bulletStyle = $slider->addStyle($params->get($this->key . 'style'), 'dot');
        $barStyle    = $slider->addStyle($params->get($this->key . 'bar'), 'simple');

        list($style, $attributes) = $this->getPosition($params, $this->key);
        $attributes['data-offset'] = $params->get($this->key . 'position-offset', 0);

        $orientation = $this->getOrientationByPosition($params->get($this->key . 'position-mode'), $params->get($this->key . 'position-area'), $params->get($this->key . 'orientation'), 'horizontal');


        $parameters = array(
            'area'       => intval($params->get($this->key . 'position-area')),
            'dotClasses' => $bulletStyle,
            'mode'       => '',
            'action'     => $params->get($this->key . 'action')
        );

        if ($params->get($this->key . 'thumbnail-show-image')) {

            $slider->exposeSlideData['thumbnail'] = true;

            $parameters['thumbnail']       = 1;
            $parameters['thumbnailWidth']  = intval($params->get($this->key . 'thumbnail-width'));
            $parameters['thumbnailHeight'] = intval($params->get($this->key . 'thumbnail-height'));
            $parameters['thumbnailStyle']  = $slider->addStyle($params->get($this->key . 'thumbnail-style'), 'simple', '');
            $side                          = $params->get($this->key . 'thumbnail-side');


            if ($side == 'before') {
                if ($orientation == 'vertical') {
                    $position = 'left';
                } else {
                    $position = 'top';
                }
            } else {
                if ($orientation == 'vertical') {
                    $position = 'right';
                } else {
                    $position = 'bottom';
                }
            }
            $parameters['thumbnailPosition'] = $position;
        }

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetBulletTransition(this, ' . json_encode($parameters) . ');');

        $fullSize = intval($params->get($this->key . 'bar-full-size'));

        return Html::tag("div", $displayAttributes + $attributes + array(
                "class" => $displayClass . ' n2-flex n2-ss-control-bullet n2-ss-control-bullet-' . $orientation . ($fullSize ? ' n2-ss-control-bullet-fullsize' : ''),
                "style" => $style
            ), Html::tag("div", array(
            "class" => $barStyle . " nextend-bullet-bar n2-ow n2-bar-justify-content-" . $params->get($this->key . 'align')
        ), ''));
    }
}