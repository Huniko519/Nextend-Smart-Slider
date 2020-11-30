<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Twitter;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Twitter\Sources\TwitterTimeline;

class GeneratorGroupTwitter extends AbstractGeneratorGroup {

    protected $name = 'twitter';

    protected $needConfiguration = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationTwitter($this);
    }

    public function getLabel() {
        return 'Twitter';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Twitter tweets');
    }

    protected function loadSources() {

        new TwitterTimeline($this, 'timeline', 'Latest tweets');
    }
}