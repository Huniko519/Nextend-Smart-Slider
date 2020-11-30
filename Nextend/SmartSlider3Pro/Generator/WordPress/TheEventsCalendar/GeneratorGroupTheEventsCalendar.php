<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Sources\TheEventsCalendarEvents;

class GeneratorGroupTheEventsCalendar extends AbstractGeneratorGroup {

    protected $name = 'theeventscalendar';

    protected $url = 'https://wordpress.org/plugins/the-events-calendar/';

    public function getLabel() {
        return 'The Events Calendar';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s events.'), 'The Events Calendar');
    }

    protected function loadSources() {

        new TheEventsCalendarEvents($this, 'events', n2_('Events'));
    }

    public function isInstalled() {
        return class_exists('Tribe__Events__Main', false);
    }

}
