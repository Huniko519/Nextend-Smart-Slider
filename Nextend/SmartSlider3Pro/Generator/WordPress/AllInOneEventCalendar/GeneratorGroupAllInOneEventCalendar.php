<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\AllInOneEventCalendar;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\AllInOneEventCalendar\Sources\AllinOneEventCalendarEvents;


class GeneratorGroupAllInOneEventCalendar extends AbstractGeneratorGroup {

    protected $name = 'allinoneeventcalendar';

    protected $url = 'https://wordpress.org/plugins/all-in-one-event-calendar/';

    public function getLabel() {
        return 'All-in-One Event Calendar';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s events.'), "All-in-One Event Calendar");
    }

    protected function loadSources() {

        new AllinOneEventCalendarEvents($this, 'events', n2_('Events'));
    }


    public function isInstalled() {
        return defined('AI1EC_PATH');
    }

}
