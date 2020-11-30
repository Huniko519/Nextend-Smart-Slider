<?php


namespace Nextend\SmartSlider3\Widget\Thumbnail\Basic;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Widget\AbstractWidgetFrontend;

class ThumbnailBasicFrontend extends AbstractWidgetFrontend {

    public function getPositions(&$params) {
        $positions                       = array();
        $positions['thumbnail-position'] = array(
            $this->key . 'position-',
            'thumbnail'
        );

        return $positions;
    }

    public function render($slider, $id, $params) {
        $showThumbnail   = intval($params->get($this->key . 'show-image'));
        $showTitle       = intval($params->get($this->key . 'title'));
        $showDescription = intval($params->get($this->key . 'description'));

        if (!$showThumbnail && !$showTitle && !$showDescription) {
            // Nothing to show
            return '';
        }

        $slider->addLess(self::getAssetsPath() . '/style.n2less', array(
            "sliderid" => $slider->elementId
        ));

        $parameters = array(
            'area'                  => intval($params->get($this->key . 'position-area')),
            'action'                => $params->get($this->key . 'action'),
            'minimumThumbnailCount' => max(1, intval($params->get($this->key . 'minimum-thumbnail-count'))) + 0.5,
            'group'                 => max(1, intval($params->get($this->key . 'group'))),
            'invertGroupDirection'  => intval($params->get('widget-thumbnail-invert-group-direction', 0)),
        );

        list($displayClass, $displayAttributes) = $this->getDisplayAttributes($params, $this->key);
        list($style, $attributes) = $this->getPosition($params, $this->key);
        $attributes['data-offset'] = $params->get($this->key . 'position-offset', 0);

        $barStyle   = $slider->addStyle($params->get($this->key . 'style-bar'), 'simple');
        $slideStyle = $slider->addStyle($params->get($this->key . 'style-slides'), 'dot');

        $width  = intval($slider->params->get($this->key . 'width', 160));
        $height = intval($slider->params->get($this->key . 'height', 100));


        $captionPlacement = $slider->params->get($this->key . 'caption-placement', 'overlay');
        if (!$showThumbnail) {
            $captionPlacement = 'before';
        }

        if (!$showTitle && !$showDescription) {
            $captionPlacement = 'overlay';
        }

        $parameters['captionSize'] = intval($slider->params->get($this->key . 'caption-size', 100));


        $orientation               = $params->get($this->key . 'orientation');
        $orientation               = $this->getOrientationByPosition($params->get($this->key . 'position-mode'), $params->get($this->key . 'position-area'), $orientation, 'vertical');
        $parameters['orientation'] = $orientation;

        $captionExtraStyle = '';
        switch ($captionPlacement) {
            case 'before':
            case 'after':
                switch ($orientation) {
                    case 'vertical':
                        if (!$showThumbnail) {
                            $width = 0;
                        }
                        $containerStyle    = "width: " . ($width + $parameters['captionSize']) . "px; height: {$height}px;";
                        $captionExtraStyle .= "width: " . $parameters['captionSize'] . "px";
                        break;
                    default:
                        if (!$showThumbnail) {
                            $height = 0;
                        }
                        $containerStyle    = "width: {$width}px; height: " . ($height + $parameters['captionSize']) . "px;";
                        $captionExtraStyle .= "height: " . $parameters['captionSize'] . "px";
                }
                break;
            default:
                $containerStyle            = "width: {$width}px; height: {$height}px;";
                $parameters['captionSize'] = 0;
        }


        $parameters['slideStyle']     = $slideStyle;
        $parameters['containerStyle'] = $containerStyle;

        if ($showThumbnail) {
            $slider->exposeSlideData['thumbnail']     = true;
            $slider->exposeSlideData['thumbnailType'] = true;

            $thumbnailCSS   = array(
                'background-size',
                'background-repeat',
                'background-position'
            );
            $thumbnailStyle = json_decode($params->get($this->key . 'style-slides'));
            if (!empty($thumbnailStyle) && !empty($thumbnailStyle->data[0]->extra)) {
                $extraCSS      = $thumbnailStyle->data[0]->extra;
                $thumbnailCode = '';
                foreach ($thumbnailCSS as $css) {
                    $currentCode = $this->getStringBetween($extraCSS, $css . ':', ';');
                    if (!empty($currentCode)) {
                        $thumbnailCode .= $css . ':' . $currentCode . ';';
                    }
                }
            } else {
                $thumbnailCode = '';
            }

            $parameters['thumbnail'] = array(
                'width'  => $width,
                'height' => $height,
                'code'   => $thumbnailCode
            );
        }

        if ($showTitle || $showDescription) {
            $parameters['caption'] = array(
                'styleClass' => $slider->addStyle($params->get($this->key . 'title-style'), 'simple'),
                'placement'  => $captionPlacement,
                'style'      => $captionExtraStyle
            );
        }

        if ($showTitle) {
            $parameters['title']              = array(
                'font' => $slider->addFont($params->get($this->key . 'title-font'), 'simple'),
            );
        }

        if ($showDescription) {
            $slider->exposeSlideData['description'] = true;
            $parameters['description']              = array(
                'font' => $slider->addFont($params->get($this->key . 'description-font'), 'simple')
            );
        }

        if ($orientation == 'vertical') {
            $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/thumbnail-vertical.min.js'));
        
            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetThumbnailDefaultVertical(this, ' . json_encode($parameters) . ');');
        } else {
            $slider->features->addInitCallback(Filesystem::readFile(self::getAssetsPath() . '/dist/thumbnail-horizontal.min.js'));
        
            $slider->features->addInitCallback('new N2Classes.SmartSliderWidgetThumbnailDefaultHorizontal(this, ' . json_encode($parameters) . ');');
        }

        $size = $params->get($this->key . 'size');
        if ($orientation == 'horizontal') {
            if (is_numeric($size) || substr($size, -1) == '%' || substr($size, -2) == 'px') {
                $style .= 'width:' . $size . ';';
                if (substr($size, -1) == '%') {
                    $attributes['data-width-percent'] = substr($size, 0, -1);
                }
            }
        } else {
            if (is_numeric($size) || substr($size, -1) == '%' || substr($size, -2) == 'px') {
                $style .= 'height:' . $size . ';';
                if (substr($size, -1) == '%') {
                    $attributes['data-height-percent'] = substr($size, 0, -1);
                }
            }
        }

        $previous  = $next = '';
        $showArrow = intval($slider->params->get($this->key . 'arrow', 1));
        if ($showArrow) {
            $arrowImagePrevious = $arrowImageNext = ResourceTranslator::toUrl($slider->params->get($this->key . 'arrow-image', ''));
            $arrowWidth         = intval($slider->params->get($this->key . 'arrow-width', 26));
            $commonStyle        = '';
            if (!empty($arrowWidth)) {
                $commonStyle = 'width:' . $arrowWidth . 'px;';
                $arrowOffset = intval($slider->params->get($this->key . 'arrow-offset', 0));
                $marginValue = -($arrowWidth / 2) + $arrowOffset;
                switch ($orientation) {
                    case 'vertical':
                        $commonStyle .= 'margin-left:' . $marginValue . 'px!important;';
                        break;
                    default:
                        $commonStyle .= 'margin-top:' . $marginValue . 'px!important;';
                }
            }
            $previousStyle = $nextStyle = $commonStyle;
            if (empty($arrowImagePrevious)) {
                $arrowImagePrevious = 'data:image/svg+xml;base64,' . Base64::encode(Filesystem::readFile(self::getAssetsPath() . '/thumbnail-up-arrow.svg'));
            } else {
                switch ($orientation) {
                    case 'vertical':
                        $previousStyle .= 'transform:rotateY(180deg) rotateX(180deg);';
                        break;
                    default:
                        $previousStyle .= 'transform:rotateZ(180deg) rotateX(180deg);';
                }
            }
            if (empty($arrowImageNext)) {
                $arrowImageNext = 'data:image/svg+xml;base64,' . Base64::encode(Filesystem::readFile(self::getAssetsPath() . '/thumbnail-down-arrow.svg'));
            } else {
                $nextStyle .= 'transform:none;';
            }

            $previous = Html::image($arrowImagePrevious, $slider->params->get($this->key . 'arrow-prev-alt', 'previous arrow'), Html::addExcludeLazyLoadAttributes(array(
                'class' => 'nextend-thumbnail-button nextend-thumbnail-previous n2-ow',
                'style' => $previousStyle
            )));
            $next     = Html::image($arrowImageNext, $slider->params->get($this->key . 'arrow-next-alt', 'next arrow'), Html::addExcludeLazyLoadAttributes(array(
                'class' => 'nextend-thumbnail-button nextend-thumbnail-next n2-ow n2-active',
                'style' => $nextStyle
            )));
        }

        if ($params->get($this->key . 'position-mode') == 'simple' && $orientation == 'vertical') {
            $area = $params->get($this->key . 'position-area');
            switch ($area) {
                case '5':
                case '6':
                case '7':
                case '8':
                    $attributes['data-sstop'] = '0';
                    break;
            }
        }


        return Html::tag('div', $displayAttributes + $attributes + array(
                'class' => $displayClass . 'nextend-thumbnail nextend-thumbnail-default n2-ow nextend-thumbnail-' . $orientation,
                'style' => $style
            ), $previous . $next . Html::tag('div', array(
                'class' => 'nextend-thumbnail-inner n2-ow ' . $barStyle
            ), Html::tag('div', array(
                'class' => 'nextend-thumbnail-scroller n2-ow n2-align-content-' . $params->get('widget-thumbnail-align-content'),
            ), '')));
    }

    private function getStringBetween($string, $start, $end) {
        $string = ' ' . $string;
        $ini    = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;

        return substr($string, $ini, $len);
    }
}