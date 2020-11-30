<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\NextgenGallery;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\NextgenGallery\Sources\NextgenGalleryGallery;

class GeneratorGroupNextgenGallery extends AbstractGeneratorGroup {

    protected $name = 'nextgengallery';

    protected $url = 'https://wordpress.org/plugins/nextgen-gallery/';

    public function getLabel() {
        return 'NextGEN Gallery';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'NextGEN Gallery');
    }

    protected function loadSources() {

        new NextgenGalleryGallery($this, 'gallery', n2_('Images'));
    }

    public function isInstalled() {
        return class_exists('nggGallery', false) || class_exists('C_Component_Registry', false);
    }
}