<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\OxygenBuilder;


use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;

class OxygenBuilder {

    public function __construct() {
        if (defined('CT_VERSION')) {
            if (isset($_REQUEST['action'])) {
                if ($_REQUEST['action'] == 'ct_render_shortcode' || $_REQUEST['action'] == 'ct_get_post_data') {
                    self::forceShortcodeIframe();
                }
            }
        }
    }

    public function forceShortcodeIframe() {

        Shortcode::forceIframe('OxygenBuilder', true);
    }
}