<?php


namespace Nextend\SmartSlider3\Slider\Feature;


use Nextend\Framework\Image\ImageEdit;
use Nextend\Framework\Image\ImageManager;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Slider\Slide;

class SlideBackground {

    private $slider;

    public function __construct($slider) {

        $this->slider = $slider;
    }

    public function makeJavaScriptProperties(&$properties) {
        $enabled = intval($this->slider->params->get('slide-background-parallax', 0));
        if (!$enabled) {
            if ($this->slider->params->get('backgroundMode') == 'fixed') {
                $enabled = 1;
                $this->slider->params->set('slide-background-parallax-strength', 100);
            }
        }
        if ($enabled) {
            $properties['backgroundParallax'] = array(
                'strength' => intval($this->slider->params->get('slide-background-parallax-strength', 50)) / 100,
                'tablet'   => intval($this->slider->params->get('bg-parallax-tablet', 0)),
                'mobile'   => intval($this->slider->params->get('bg-parallax-mobile', 0))
            );
        }
    }

    /**
     * @param $slide Slide
     *
     * @return string
     */

    public function make($slide) {

        if ($slide->parameters->get('background-type') == '') {
            $slide->parameters->set('background-type', 'color');
            if ($slide->parameters->get('backgroundVideoMp4')) {
                $slide->parameters->set('background-type', 'video');
            } else if ($slide->parameters->get('backgroundImage')) {
                $slide->parameters->set('background-type', 'image');
            }
        }

        $html = $this->makeBackground($slide);

        return $html;
    }

    private function getBackgroundStyle($slide) {

        $style = '';
        $color = $slide->fill($slide->parameters->get('backgroundColor', ''));
        if (strlen($color) > 0 && $color[0] == '#') {
            $color = substr($color, 1);
            if (strlen($color) == 6) {
                $color .= 'ff';
            }
        }
        $gradient = $slide->parameters->get('backgroundGradient', 'off');

        if ($gradient != 'off') {
            $colorEnd = $slide->fill($slide->parameters->get('backgroundColorEnd', 'ffffff00'));
            if (strlen($colorEnd) > 0 && $colorEnd[0] == '#') {
                $colorEnd = substr($colorEnd, 1);
                if (strlen($colorEnd) == 6) {
                    $colorEnd .= 'ff';
                }
            }
            switch ($gradient) {
                case 'horizontal':
                    $style .= 'background:linear-gradient(to right, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'vertical':
                    $style .= 'background:linear-gradient(to bottom, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal1':
                    $style .= 'background:linear-gradient(45deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal2':
                    $style .= 'background:linear-gradient(135deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
            }
        } else {
            if (strlen($color) == 8) {

                $alpha = substr($color, 6, 2);
                if ($alpha != '00') {
                    $style = 'background-color: #' . substr($color, 0, 6) . ';';
                    if ($alpha != 'ff') {
                        $style .= "background-color: " . Color::colorToRGBA($color) . ";";
                    }
                }
            }
        }

        return $style;
    }

    private function makeBackground($slide) {

        $backgroundType = $slide->parameters->get('background-type');

        if (empty($backgroundType)) {
            $backgroundVideoMp4 = $slide->parameters->get('backgroundVideoMp4', '');
            $backgroundImage    = $slide->parameters->get('backgroundImage', '');
            if (!empty($backgroundVideoMp4)) {
                $backgroundType = 'video';
            } else if (!empty($backgroundImage)) {
                $backgroundType = 'image';
            } else {
                $backgroundType = 'color';
            }
        }

        $backgroundElements = array();

        if ($backgroundType == 'color') {
            $backgroundElements[] = $this->renderColor($slide);

        } else if ($backgroundType == 'video') {


            $backgroundElements[] = $this->renderBackgroundVideo($slide);
            $backgroundElements[] = $this->renderImage($slide);

            $backgroundElements[] = $this->renderColor($slide);

        } else if ($backgroundType == 'image') {


            $backgroundElements[] = $this->renderImage($slide);

            $backgroundElements[] = $this->renderColor($slide);
        }

        $fillMode = $slide->parameters->get('backgroundMode', 'default');
        if ($fillMode == 'default') {
            $fillMode = $this->slider->params->get('backgroundMode', 'fill');
        }

        if ($fillMode == 'fixed') {
            $fillMode = 'fill';
        }

        return Html::tag('div', array(
            'class'     => "n2-ss-slide-background n2-ow",
            'data-mode' => $fillMode
        ), implode('', $backgroundElements));
    }

    private function renderColor($slide) {
        $backgroundColorStyle = $this->getBackgroundStyle($slide);

        if (!empty($backgroundColorStyle)) {

            $attributes = array(
                'class' => 'n2-ss-slide-background-color',
                "style" => $backgroundColorStyle
            );

            if ($slide->parameters->get('backgroundColorOverlay', 0)) {
                $attributes['data-overlay'] = 1;
            }

            return Html::tag('div', $attributes, '');
        }

        return '';
    }

    /**
     * @param $slide Slide
     *
     * @return string
     */
    private function renderImage($slide) {

        $rawBackgroundImage = $slide->parameters->get('backgroundImage', '');

        if (empty($rawBackgroundImage)) {
            return '';
        }

        $backgroundImageBlur = max(0, $slide->parameters->get('backgroundImageBlur', 0));

        $x = max(0, min(100, $slide->fill($slide->parameters->get('backgroundFocusX', 50))));
        $y = max(0, min(100, $slide->fill($slide->parameters->get('backgroundFocusY', 50))));

        if ($slide->hasGenerator()) {

            $backgroundImage = $slide->fill($rawBackgroundImage);

            $imageData = ImageManager::getImageData($rawBackgroundImage);

            $imageData['desktop-retina']['image'] = $slide->fill($imageData['desktop-retina']['image']);
            $imageData['tablet']['image']         = $slide->fill($imageData['tablet']['image']);
            $imageData['tablet-retina']['image']  = $slide->fill($imageData['tablet-retina']['image']);
            $imageData['mobile']['image']         = $slide->fill($imageData['mobile']['image']);
            $imageData['mobile-retina']['image']  = $slide->fill($imageData['mobile-retina']['image']);
        } else {
            $backgroundImage = $slide->fill($rawBackgroundImage);
            $imageData       = ImageManager::getImageData($backgroundImage);
        }

        if (empty($backgroundImage)) {
            $src = ImageEdit::base64Transparent();
        } else {
            $src = ResourceTranslator::urlToResource($this->slider->features->optimize->optimizeBackground($backgroundImage, $x, $y));
            $slide->addImage(ResourceTranslator::toUrl($src));
        }


        $alt   = $slide->fill($slide->parameters->get('backgroundAlt', ''));
        $title = $slide->fill($slide->parameters->get('backgroundTitle', ''));

        $deviceAttributes = $this->getDeviceAttributes($src, $imageData);

        $opacity = min(100, max(0, $slide->parameters->get('backgroundImageOpacity', 100)));

        $style = array();
        if ($opacity < 100) {
            $style[] = 'opacity:' . ($opacity / 100);
        }

        if ($x != '50' || $y != '50') {
            $style[] = 'background-position: ' . $x . '% ' . $y . '%';
        }

        $attributes = $deviceAttributes + array(
                "class"      => 'n2-ss-slide-background-image',
                "data-blur"  => $backgroundImageBlur,
                "data-alt"   => $alt,
                "data-title" => $title
            );

        if (!$slide->getSlider()->features->lazyLoad->isEnabled && !empty($attributes['data-desktop']) && empty($attributes['data-tablet']) && empty($attributes['data-mobile'])) {
            $style[] = 'background-image:url(\'' . $attributes['data-desktop'] . '\')';

            /**
             * Fix for Autoptimize lazy load images
             */
            $attributes['data-no-lazy'] = '';
        }

        if (!empty($style)) {
            $attributes['style'] = implode(';', $style);
        }

        if ($slide->isCurrentlyEdited()) {
            $attributes['data-opacity'] = $opacity;
            $attributes['data-x']       = $x;
            $attributes['data-y']       = $y;
        }

        return Html::tag('div', $attributes, '');
    }

    private function getDeviceAttributes($image, $imageData) {

        $attributes                 = array();
        $attributes['data-hash']    = md5($image);
        $attributes['data-desktop'] = ResourceTranslator::toUrl($image);

        if ($imageData['desktop-retina']['image'] == '' && $imageData['tablet']['image'] == '' && $imageData['tablet-retina']['image'] == '' && $imageData['mobile']['image'] == '' && $imageData['mobile-retina']['image'] == '') {

        } else {
            if ($imageData['desktop-retina']['image'] != '') {
                $attributes['data-desktop-retina'] = ResourceTranslator::toUrl($imageData['desktop-retina']['image']);
            }
            if ($imageData['tablet']['image'] != '') {
                $attributes['data-tablet'] = ResourceTranslator::toUrl($imageData['tablet']['image']);
            }
            if ($imageData['tablet-retina']['image'] != '') {
                $attributes['data-tablet-retina'] = ResourceTranslator::toUrl($imageData['tablet-retina']['image']);
            }
            if ($imageData['mobile']['image'] != '') {
                $attributes['data-mobile'] = ResourceTranslator::toUrl($imageData['mobile']['image']);
            }
            if ($imageData['mobile-retina']['image'] != '') {
                $attributes['data-mobile-retina'] = ResourceTranslator::toUrl($imageData['mobile-retina']['image']);
            }

            //We have to force the fade on load enabled to make sure the user get great result.
            $this->slider->features->fadeOnLoad->forceFadeOnLoad();
        }

        return $attributes;
    }

    /**
     * @param Slide $slide
     *
     * @return string
     */
    private function renderBackgroundVideo($slide) {
        $mp4 = ResourceTranslator::toUrl($slide->fill($slide->parameters->get('backgroundVideoMp4', '')));

        if (empty($mp4)) {
            return '';
        }

        $sources = '';

        if ($mp4) {
            $sources .= Html::tag("source", array(
                "src"  => $mp4,
                "type" => "video/mp4"
            ), '', false);
        }

        $opacity = min(100, max(0, $slide->parameters->get('backgroundVideoOpacity', 100)));

        $attributes = array(
            'class'              => 'n2-ss-slide-background-video n2-ow intrinsic-ignore',
            'style'              => 'opacity:' . ($opacity / 100) . ';',
            'data-mode'          => $slide->parameters->get('backgroundVideoMode', 'fill'),
            'playsinline'        => 1,
            'webkit-playsinline' => 1,
            'onloadstart'        => 'this.n2LoadStarted=1;',
            'data-keepplaying'   => 1,
            'preload'            => 'none'
        );

        if ($slide->parameters->get('backgroundVideoMuted', 1)) {
            $attributes['muted'] = 'muted';
        }

        if ($slide->parameters->get('backgroundVideoLoop', 1)) {
            $attributes['loop'] = 'loop';
        }

        if ($slide->parameters->get('backgroundVideoReset', 1)) {
            $attributes['data-reset-slide-change'] = 1;
        }

        return Html::tag('video', $attributes, $sources);
    

        return '';
    }
}