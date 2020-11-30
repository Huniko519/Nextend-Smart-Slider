<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Sources\EventsManagerEvents;

class GeneratorGroupEventsManager extends AbstractGeneratorGroup {

    protected $name = 'eventsmanager';

    protected $url = 'https://wordpress.org/plugins/events-manager/';

    public function getLabel() {
        return 'Events Manager';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s events.'), 'Events Manager');
    }

    protected function loadSources() {

        new EventsManagerEvents($this, 'events', n2_('Events'));
    }

    public function isInstalled() {
        return class_exists('EM_Events', false);
    }

}