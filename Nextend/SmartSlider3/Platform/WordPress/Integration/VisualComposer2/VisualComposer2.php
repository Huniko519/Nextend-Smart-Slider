<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\VisualComposer2;

use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;

class VisualComposer2 {

    public function __construct() {
        if (class_exists('VcvEnv') && isset($_REQUEST['vcv-ajax']) && $_REQUEST['vcv-ajax'] == 1) {
            Shortcode::forceIframe('VisualComposer2', true);
        }
    }
}