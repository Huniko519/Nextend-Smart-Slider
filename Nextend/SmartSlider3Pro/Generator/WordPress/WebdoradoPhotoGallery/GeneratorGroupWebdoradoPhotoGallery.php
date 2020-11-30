<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Sources\WebdoradoPhotoGalleryImages;

class GeneratorGroupWebdoradoPhotoGallery extends AbstractGeneratorGroup {

    protected $name = 'webdorado';

    protected $url = 'https://wordpress.org/plugins/photo-gallery/';

    public function getLabel() {
        return 'Photo Gallery by 10Web';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'Photo Gallery by 10Web');
    }

    protected function loadSources() {

        new WebdoradoPhotoGalleryImages($this, 'images', n2_('Images'));
    }

    public function isInstalled() {
        return is_plugin_active('photo-gallery/photo-gallery.php');
    }

}
