<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\Brizy;

use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;

class Brizy {

    public function __construct() {
        if (class_exists('Brizy_Editor') && isset($_REQUEST['action']) && $_REQUEST['action'] == \Brizy_Editor::prefix() . '_shortcode_content') {
            Shortcode::forceIframe('Brizy', true);
        }
    }
}