<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Rss;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Rss\Sources\RSSFeed;

class GeneratorGroupRss extends AbstractGeneratorGroup {

    protected $name = 'rss';

    public function getLabel() {
        return 'RSS';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'RSS');
    }

    protected function loadSources() {

        new RSSFeed($this, 'feed', 'RSS Feed');
    }
}