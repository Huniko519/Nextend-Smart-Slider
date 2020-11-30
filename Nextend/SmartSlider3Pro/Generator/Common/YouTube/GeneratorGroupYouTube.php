<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\Sources\YouTubeByPlaylist;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\Sources\YouTubeBySearch;

class GeneratorGroupYouTube extends AbstractGeneratorGroup {

    protected $name = 'youtube';

    protected $needConfiguration = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationYoutube($this);
    }

    public function getLabel() {
        return 'YouTube';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'YouTube');
    }

    protected function loadSources() {

        new YouTubeBySearch($this, 'bysearch', n2_('Search'));
        new YouTubeByPlaylist($this, 'byplaylist', n2_('Playlist'));
    }
}
