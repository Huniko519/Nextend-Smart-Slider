<?php


namespace Nextend\SmartSlider3\Slider\Feature;


use Exception;
use Nextend\Framework\Image\ImageEdit;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;

class Optimize {

    private $slider;

    private $optimize = false, $thumbnailWidth = 100, $thumbnailHeight = 60, $quality = 70;

    private $backgroundImageCustom = false;
    private $backgroundImageWidth = 800;
    private $backgroundImageHeight = 600;

    public function __construct($slider) {

        $this->slider = $slider;

        $this->optimize = $slider->params->get('optimize', 0);

        $this->backgroundImageCustom = intval($slider->params->get('optimize-background-image-custom', 0));
        $this->backgroundImageWidth  = intval($slider->params->get('optimize-background-image-width', 800));
        $this->backgroundImageHeight = intval($slider->params->get('optimize-background-image-height', 600));
        if ($this->backgroundImageWidth < 50 || $this->backgroundImageHeight < 50) {
            $this->backgroundImageCustom = false;
        }

        $this->thumbnailWidth  = $slider->params->get('optimizeThumbnailWidth', 100);
        $this->thumbnailHeight = $slider->params->get('optimizeThumbnailHeight', 60);

        $this->quality = intval($slider->params->get('optimize-quality', 70));
    }

    public function optimizeBackground($image, $x = 50, $y = 50) {
        if ($this->optimize) {
            try {
                $sizes = $this->slider->assets->sizes;

                return ImageEdit::resizeImage('resized', ResourceTranslator::toPath($image), ($this->backgroundImageCustom ? $this->backgroundImageWidth : $sizes['canvasWidth']), ($this->backgroundImageCustom ? $this->backgroundImageHeight : $sizes['canvasHeight']), false, 'normal', 'ffffff', true, $this->quality, true, $x, $y);
            } catch (Exception $e) {
                return $image;
            }
        }

        return $image;
    }

    public function optimizeThumbnail($image) {
        if ($this->optimize) {
            try {
                return ImageEdit::resizeImage('resized', ResourceTranslator::toPath($image), $this->thumbnailWidth, $this->thumbnailHeight, false, 'normal', 'ffffff', true, $this->quality, true);
            } catch (Exception $e) {

                return ResourceTranslator::toUrl($image);
            }
        }

        return ResourceTranslator::toUrl($image);
    }

    public function adminOptimizeThumbnail($image) {
        if ($this->optimize) {
            try {
                return ImageEdit::resizeImage('resized', ResourceTranslator::toPath($image), $this->thumbnailWidth, $this->thumbnailHeight, true, 'normal', 'ffffff', true, $this->quality, true);
            } catch (Exception $e) {

                return ResourceTranslator::toUrl($image);
            }
        }

        return ResourceTranslator::toUrl($image);
    }
}