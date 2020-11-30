<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\Fusion;


use Fusion_Element;
use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;

class FusionElementSmartSlider3 extends Fusion_Element {

    public function __construct() {
        parent::__construct();

        add_action('wp_ajax_get_shortcode_render', array(
            $this,
            'force_iframe'
        ));

        add_shortcode('fusion_smartslider3', array(
            $this,
            'render'
        ));
    }

    public function render($args, $content = '') {

        return do_shortcode('[smartslider3 slider="' . $args['slider'] . '"]');
    }

    public function force_iframe() {
        Shortcode::forceIframe('fusion', true);
    }
}