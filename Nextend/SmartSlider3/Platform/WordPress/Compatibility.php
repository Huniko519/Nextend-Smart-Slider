<?php


namespace Nextend\SmartSlider3\Platform\WordPress;


use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;
use Nextend\SmartSlider3\Settings;

class Compatibility {

    public function __construct() {

        /**
         * Fix for NextGenGallery and Divi live editor bug
         */
        add_filter('run_ngg_resource_manager', function ($ret) {
            if (isset($_GET['n2prerender']) && isset($_GET['n2app'])) {
                $ret = false;
            }

            return $ret;
        }, 1000000);


        /**
         * For ajax based page loaders
         *
         * HTTP_X_BARBA -> Rubenz theme
         * swup -> Etc @see https://themeforest.net/item/etc-agency-freelance-portfolio-wordpress-theme/23832736
         */

        $xRequestedWiths = array(
            'XMLHttpRequest',
            'swup'
        );

        if ((isset($_SERVER['HTTP_X_REQUESTED_WITH']) && in_array($_SERVER['HTTP_X_REQUESTED_WITH'], $xRequestedWiths)) || isset($_SERVER['HTTP_X_BARBA'])) {

            if (intval(Settings::get('wp-ajax-iframe-slider', 0))) {
                Shortcode::forceIframe('ajax');
            }
        }


        add_action('load-toplevel_page_' . NEXTEND_SMARTSLIDER_3_URL_PATH, array(
            $this,
            'removeEmoji'
        ));


        /**
         * Yoast SEO - Sitemap add images
         */
        if (Settings::get('yoast-sitemap', 1)) {
            add_filter('wpseo_xml_sitemap_post_url', array(
                $this,
                'filter_wpseo_xml_sitemap_post_url'
            ), 10, 2);
        }


        /**
         * Not sure which page builder is it...
         */
        if (isset($_GET['pswLoad']) && $_GET['pswLoad'] == 1) {
            Shortcode::forceIframe('psw');
        }

        /*
         * WP Rocket remove from exclusion
         */
        if (defined('WP_ROCKET_VERSION') && version_compare(WP_ROCKET_VERSION, '3.7.1.1') < 1) {
            add_filter('rocket_excluded_inline_js_content', array(
                $this,
                'remove_rocket_excluded_inline_js_content'
            ));
        }
    }

    public function removeEmoji() {

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
    }

    public static function filter_wpseo_xml_sitemap_post_url($permalink, $post) {
        global $shortcode_tags;
        $_shortcode_tags    = $shortcode_tags;
        $shortcode_tags     = array(
            "smartslider3" => array(
                Shortcode::class,
                "doShortcode"
            )
        );
        $post->post_content = do_shortcode($post->post_content);
        $shortcode_tags     = $_shortcode_tags;

        return $permalink;
    }

    public function remove_rocket_excluded_inline_js_content($excluded_inline) {

        if (($index = array_search('SmartSliderSimple', $excluded_inline)) !== false) {
            array_splice($excluded_inline, $index, 1);
        }

        return $excluded_inline;
    }
}