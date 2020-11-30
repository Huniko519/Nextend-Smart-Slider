<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Vimeo;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Sources\VimeoAlbum;

class GeneratorGroupVimeo extends AbstractGeneratorGroup {

    protected $name = 'vimeo';

    protected $needConfiguration = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationVimeo($this);
    }

    public function getLabel() {
        return 'Vimeo';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Vimeo');
    }

    protected function loadSources() {

        new VimeoAlbum($this, 'album', 'Album');
    }
}
