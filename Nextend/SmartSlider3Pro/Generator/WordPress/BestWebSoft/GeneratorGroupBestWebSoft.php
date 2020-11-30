<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\BestWebSoft;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\BestWebSoft\Sources\BestWebSoftGallery;

class GeneratorGroupBestWebSoft extends AbstractGeneratorGroup {

    protected $name = 'bestwebsoft';

    protected $url = 'https://wordpress.org/plugins/gallery-plugin/';

    public function getLabel() {
        return 'BestWebSoft Gallery';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'BestWebSoft Gallery');
    }

    protected function loadSources() {

        new BestWebSoftGallery($this, 'gallery', n2_('Images'));
    }

    public function isInstalled() {
        return function_exists('gllr_init');
    }
}
