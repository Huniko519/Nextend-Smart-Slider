<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Facebook;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Facebook\Sources\FacebookAlbums;
use Nextend\SmartSlider3Pro\Generator\Common\Facebook\Sources\FacebookPostsByPage;

class GeneratorGroupFacebook extends AbstractGeneratorGroup {

    protected $name = 'facebook';

    protected $needConfiguration = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationFacebook($this);
    }

    public function getLabel() {
        return 'Facebook';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('Facebook photos or posts on your page'));
    }

    protected function loadSources() {

        new FacebookAlbums($this, 'albums', n2_('Photos by album'));
        new FacebookPostsByPage($this, 'postsbypage', n2_('Posts by page'));
    }

    public function addSource($name, $source) {
        $this->sources[$name] = $source;
    }
}