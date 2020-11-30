<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\Elementor;


use Elementor\Plugin;
use Nextend\SmartSlider3\Platform\WordPress\HelperTinyMCE;
use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;
use Nextend\SmartSlider3\Platform\WordPress\Widget\WidgetSmartSlider3;

class Elementor {

    public function __construct() {

        add_action('elementor/init', array(
            $this,
            'init'
        ));
    }

    public function init() {

        add_filter('elementor/widgets/black_list', function ($black_list) {
            $black_list[] = 'N2SS3Widget';
            $black_list[] = WidgetSmartSlider3::class;

            return $black_list;
        });

        add_action('template_redirect', array(
            $this,
            'action_template_redirect'
        ), -1);

        add_action('admin_action_elementor', array(
            $this,
            'forceShortcodeIframe'
        ), -10000);

        add_action('wp_ajax_elementor_ajax', array(
            $this,
            'forceShortcodeIframe'
        ), -1);

        add_action('wp_ajax_elementor_render_widget', array(
            $this,
            'forceShortcodeIframe'
        ), -1);


        add_action('elementor/widgets/widgets_registered', array(
            $this,
            'action_widgets_registered'
        ), 100);

        add_action('elementor/controls/controls_registered', array(
            $this,
            'action_controls_registered'
        ));


        add_action('elementor/editor/before_enqueue_styles', array(
            $this,
            'action_editor_before_enqueue_styles'
        ));

        add_action('elementor/editor/before_enqueue_scripts', array(
            HelperTinyMCE::getInstance(),
            'addForcedFrontend'
        ));
    }

    public function action_template_redirect() {

        if (Plugin::instance()->editor->is_edit_mode() || Plugin::instance()->preview->is_preview_mode()) {
            $this->forceShortcodeIframe();
        }
    }

    public function action_widgets_registered() {

        $widget_manager = Plugin::$instance->widgets_manager;
        $widget_manager->register_widget_type(new ElementorWidgetSmartSlider());
    }

    public function action_controls_registered($controls_manager) {

        $controls_manager->register_control('smartsliderfield', new ElementorControlSmartSlider());
    }

    public function forceShortcodeIframe() {

        Shortcode::forceIframe('Elementor', true);
    }

    public function action_editor_before_enqueue_styles() {

        HelperTinyMCE::getInstance()
                     ->initButtonDialog();
    }
}