<?php


namespace Nextend\SmartSlider3\Conflict\WordPress;


use Nextend\SmartSlider3\Conflict\Conflict;
use WPH_functions;

class WordPressConflict extends Conflict {

    protected function __construct() {
        parent::__construct();

        $this->testPluginForgetAboutShortcodeButtons();
        $this->testPluginWPHideAndSecurity();
        $this->testPluginNetbaseWidgetsForSiteOrigin();
        $this->testPluginNavMenuAddonForElementor();
        $this->testPluginSimplyExclude();
    }

    /**
     * Forget About Shortcode Buttons
     * @url https://wordpress.org/plugins/forget-about-shortcode-buttons/
     */
    private function testPluginForgetAboutShortcodeButtons() {
        if (function_exists('run_forget_about_shortcode_buttons')) {
            $this->displayConflict('Forget About Shortcode Buttons', n2_('This plugin breaks JavaScript in the admin area, deactivate it and use alternative plugin.'), 'https://wordpress.org/support/topic/fasc-breaks-js-in-several-other-plugins/');
        }
    }

    /**
     * WP Hide & Security Enhancer
     * @url https://wordpress.org/plugins/wp-hide-security-enhancer/
     */
    private function testPluginWPHideAndSecurity() {
        if (class_exists('WPH', false)) {

            if (class_exists('WPH_functions', false)) {
                $functions = new WPH_functions();
                if ($functions->is_permalink_enabled()) {
                    $new_admin_url = $functions->get_module_item_setting('admin_url', 'admin');
                    if (!empty($new_admin_url)) {
                        $this->displayConflict('WP Hide & Security Enhancer', n2_('This plugin breaks Smart Slider 3 ajax calls if custom admin url enabled.'), 'https://wordpress.org/support/topic/smart-slider-3-does-not-work-with-custom-admin-url/');

                    }
                }
            }
        }
    }

    /**
     * Netbase Widgets For SiteOrigin
     * @url https://wordpress.org/plugins/netbase-widgets-for-siteorigin/
     */
    private function testPluginNetbaseWidgetsForSiteOrigin() {
        if (class_exists('NBT_SiteOrigin_Widgets')) {
            $this->displayConflict('Netbase Widgets For SiteOrigin', n2_('This plugin adds a background image to every SVG and breaks SSL.'), 'https://wordpress.org/support/topic/plugin-messes-up-svg-and-breaks-ssl/');

        }
    }

    /**
     * NavMenu Addon For Elementor
     * @url https://wordpress.org/plugins/navmenu-addon-for-elementor/
     */
    private function testPluginNavMenuAddonForElementor() {
        if (defined('ELEMENTOR_MENUS_VERSION')) {
            $this->displayConflict('NavMenu Addon For Elementor', n2_('This plugin has a JavaScript error which might break Smart Slider.'), 'https://wordpress.org/support/topic/plugin-causes-javascript-error-and-breaks-others/');

        }
    }


    /**
     * Simply Exclude
     * @url https://wordpress.org/plugins/simply-exclude/
     */
    private function testPluginSimplyExclude() {
        if (defined('SIMPLY_EXCLUDE_I18N_DOMAIN')) {
            $this->displayConflict('Simply Exclude', sprintf(n2_('This plugin breaks the filtering options of the dynamic slide generators when %1$s is set to %2$s. Also it has not received any updates since WordPress 3.9.'), 'Query Filtering', 'All Loops'));
        }
    }
}