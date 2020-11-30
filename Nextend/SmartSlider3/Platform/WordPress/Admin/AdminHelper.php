<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Admin;


use Nextend\Framework\PageFlow;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3\Application\Model\ModelLicense;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Platform\SmartSlider3Platform;
use Nextend\SmartSlider3\Platform\WordPress\HelperTinyMCE;
use Nextend\SmartSlider3\Settings;
use WP_Admin_Bar;

class AdminHelper {

    public function __construct() {

        add_action('admin_init', function () {
            if (ModelLicense::getInstance()
                            ->maybeActiveLazy()) {
                require_once dirname(__FILE__) . '/pro/noticeu.php';
            } else {
                require_once dirname(__FILE__) . '/pro/noticena.php';
            }
        
        });

        add_action('init', array(
            $this,
            'action_init'
        ));

        add_action('admin_menu', array(
            $this,
            'action_admin_menu'
        ));

        add_action('network_admin_menu', array(
            $this,
            'action_network_admin_menu'
        ));

        add_action('wp_ajax_smart-slider3', array(
            $this,
            'display_admin_ajax'
        ));

        add_filter('plugin_action_links', array(
            $this,
            'filter_plugin_action_links'
        ), 10, 2);

        add_action('save_post', array(
            $this,
            'clearSliderCache'
        ));

        add_action('wp_untrash_post', array(
            $this,
            'clearSliderCache'
        ));

        global $wp_version;
        if (version_compare($wp_version, '5.1', '<')) {
            add_action('wpmu_new_blog', array(
                $this,
                'onInsertSite'
            ), -1000000);
        } else {
            add_action('wp_insert_site', array(
                $this,
                'onInsertSite'
            ), -1000000);
        }
    }

    public function action_init() {

        if (current_user_can('smartslider_edit') && intval(Settings::get('wp-adminbar', 1))) {
            add_action('admin_bar_menu', array(
                $this,
                'action_admin_bar_menu'
            ), 81);
        }
    }

    public function action_admin_menu() {

        add_menu_page('Smart Slider', 'Smart Slider', 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH, array(
            $this,
            'display_admin'
        ), 'dashicons-smart_slider__admin_menu');

        add_submenu_page(NEXTEND_SMARTSLIDER_3_URL_PATH, 'Smart Slider', n2_('Dashboard'), 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH, array(
            $this,
            'display_admin'
        ));

        add_submenu_page(NEXTEND_SMARTSLIDER_3_URL_PATH, 'Help Center', n2_('Help center'), 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH . '-help', array(
            $this,
            'display_help'
        ));

        wp_enqueue_style('dashicons-smart-slider', HelperTinyMCE::getAssetsUri() . '/dist/wordpress-admin-menu.min.css', array('dashicons'));

    }

    public function display_controller($controller, $action = 'index', $ajax = false) {
        $application = ApplicationSmartSlider3::getInstance();

        $applicationType = $application->getApplicationTypeAdmin();
        $applicationType->processRequest($controller, $action, $ajax);

        PageFlow::markApplicationEnd();
    }

    public function display_admin() {

        $this->display_controller('sliders', 'gettingstarted');
    }

    public function display_admin_index() {

        $this->display_controller('sliders');
    }

    public function display_help() {

        $this->display_controller('help');
    }

    public function display_go_pro() {

        $this->display_controller('goPro');
    }

    public function display_admin_ajax() {

        $this->display_controller('sliders', 'index', true);
    }

    public function action_network_admin_menu() {

        add_action('admin_head', array(
            $this,
            'action_admin_head_network_update'
        ));

        add_menu_page('Smart Slider Update', 'Smart Slider Update', 'smartslider', NEXTEND_SMARTSLIDER_3_URL_PATH, array(
            $this,
            'display_network_update'
        ), 'dashicons-smart_slider__admin_menu');

        wp_enqueue_style('dashicons-smart-slider', HelperTinyMCE::getAssetsUri() . '/dist/wordpress-admin-menu.min.css', array('dashicons'));
    }

    public function action_admin_head_network_update() {

        echo '<style type="text/css">#adminmenu .toplevel_page_' . NEXTEND_SMARTSLIDER_3_URL_PATH . '{display: none;}</style>';
    }

    public function display_network_update() {

        $application     = ApplicationSmartSlider3::getInstance();
        $applicationType = $application->getApplicationTypeAdmin();
        $applicationType->process('update', 'update');

        PageFlow::markApplicationEnd();
    }

    /**
     * @param WP_Admin_Bar $wp_admin_bar
     */
    public function action_admin_bar_menu($wp_admin_bar) {
        global $wpdb;

        $adminUrl = SmartSlider3Platform::getAdminUrl();

        $wp_admin_bar->add_node(array(
            'id'     => 'new_content_smart_slider',
            'parent' => 'new-content',
            'title'  => 'Slider [Smart Slider 3]',
            'href'   => $adminUrl . '#createslider'
        ));

        $wp_admin_bar->add_node(array(
            'id'    => 'smart_slider_3',
            'title' => 'Smart Slider',
            'href'  => $adminUrl
        ));

        $wp_admin_bar->add_node(array(
            'id'     => 'smart_slider_3_dashboard',
            'parent' => 'smart_slider_3',
            'title'  => 'Dashboard',
            'href'   => $adminUrl
        ));

        $wp_admin_bar->add_node(array(
            'id'     => 'smart_slider_3_create_slider',
            'parent' => 'smart_slider_3',
            'title'  => 'Create slider',
            'href'   => $adminUrl . '#createslider'
        ));


        $query   = 'SELECT sliders.title, sliders.id, slides.thumbnail
            FROM ' . $wpdb->prefix . 'nextend2_smartslider3_sliders AS sliders
            LEFT JOIN ' . $wpdb->prefix . 'nextend2_smartslider3_slides AS slides ON slides.id = (SELECT id FROM ' . $wpdb->prefix . 'nextend2_smartslider3_slides WHERE slider = sliders.id AND published = 1 AND generator_id = 0 AND thumbnail NOT LIKE \'\' ORDER BY ordering DESC LIMIT 1)
            WHERE sliders.status = \'published\'
            ORDER BY time DESC LIMIT 10';
        $sliders = $wpdb->get_results($query, ARRAY_A);

        if (count($sliders)) {

            $wp_admin_bar->add_node(array(
                'id'     => 'smart_slider_3_edit',
                'parent' => 'smart_slider_3',
                'title'  => 'Edit slider',
                'href'   => $adminUrl
            ));

            $applicationType = ApplicationSmartSlider3::getInstance()
                                                      ->getApplicationTypeAdmin();


            foreach ($sliders as $slider) {
                $wp_admin_bar->add_node(array(
                    'id'     => 'smart_slider_3_slider_' . $slider['id'],
                    'parent' => 'smart_slider_3_edit',
                    'title'  => '#' . $slider['id'] . ' - ' . $slider['title'],
                    'href'   => $applicationType->getUrlSliderEdit($slider['id'])
                ));
            }

            if (count($sliders) == 10) {
                $wp_admin_bar->add_node(array(
                    'id'     => 'smart_slider_3_slider_view_all',
                    'parent' => 'smart_slider_3_edit',
                    'title'  => 'View all',
                    'href'   => $adminUrl
                ));
            }
        }
    }

    public function filter_plugin_action_links($links, $file) {
        if ($file === NEXTEND_SMARTSLIDER_3_BASENAME && current_user_can('manage_options')) {
            if (!is_array($links)) {
                $links = array();
            }
            $links[] = sprintf('<a href="%s">%s</a>', wp_nonce_url(add_query_arg(array('repairss3' => '1'), SmartSlider3Platform::getAdminUrl()), 'repairss3'), 'Analyze & Repair');
        }

        return $links;
    }

    public function clearSliderCache() {

        $applicationTypeAdmin = ApplicationSmartSlider3::getInstance()
                                                       ->getApplicationTypeAdmin();

        $slidersModel = new ModelSliders($applicationTypeAdmin);
        $slidersModel->invalidateCache();
    }

    public function onInsertSite() {

        remove_action('save_post', array(
            $this,
            'clearSliderCache'
        ));
        remove_action('wp_untrash_post', array(
            $this,
            'clearSliderCache'
        ));
    }
}