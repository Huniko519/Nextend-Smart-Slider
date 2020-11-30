<?php


namespace Nextend\SmartSlider3\Widget\Shadow\ShadowImage;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ShadowImageFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions                    = array();
        $positions['shadow-position'] = array(
            $this->key . 'position-',
            'shadow'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {

        $shadow = $params->get($this->key . 'shadow-image');
        if (empty($shadow)) {
            $shadow = $params->get($this->key . 'shadow');
            if ($shadow == -1) {
                $shadow = null;
            } else {
                $shadow = self::getAssetsUri() . '/shadow/' . basename($shadow);
            }
        }
        if (!$shadow) {
            return '';
        }

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));
        $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/shadow.min.js'));
    


        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);

        list($style, $attributes) = $this->getPosition($params, $this->key);

        $width = $params->get($this->key . 'width');
        if (is_numeric($width) || substr($width, -1) == '%' || substr($width, -2) == 'px') {
            $style .= 'width:' . $width . ';';
        }

        $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetShadow(this);');


        return Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . "nextend-shadow n2-ow",
                'style' => $style
            ), Html::image(ResourceTranslator::toUrl($shadow), 'Shadow', Html::addExcludeLazyLoadAttributes(array(
            'style' => 'display: block; width:100%;max-width:none;',
            'class' => 'n2-ow nextend-shadow-image'
        ))));
    }
}