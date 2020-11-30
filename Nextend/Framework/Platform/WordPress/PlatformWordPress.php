<?php


namespace Nextend\Framework\Platform\WordPress;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Platform\AbstractPlatform;
use Nextend\Framework\Url\Url;

class PlatformWordPress extends AbstractPlatform {

    protected $hasPosts = true;

    public function __construct() {

        if (is_admin() || $this->isBeaverBuilderActive()) {
            $this->isAdmin = true;
        }
    }

    public function getName() {

        return 'wordpress';
    }

    public function getLabel() {

        return 'WordPress';
    }

    public function getVersion() {
        global $wp_version;

        return $wp_version;
    }

    public function getSiteUrl() {

        return str_replace(array(
            'http://',
            'https://'
        ), '//', site_url('/'));
    }

    public function getCharset() {

        return get_option('blog_charset');
    }

    public function getMysqlDate() {

        return current_time('mysql');
    }

    public function getTimestamp() {

        return current_time('timestamp');
    }

    public function filterAssetsPath($assetsPath) {

        return str_replace(SMARTSLIDER3_LIBRARY_PATH, NEXTEND_SMARTSLIDER_3 . 'Public', $assetsPath);
    }

    public function getPublicDirectory() {

        $upload_dir = wp_upload_dir();

        if (!stream_is_local($upload_dir['basedir'])) {
            return $upload_dir['basedir'];
        }

        return Filesystem::convertToRealDirectorySeparator(str_replace('//', '/', $upload_dir['basedir']));
    }

    public function getUserEmail() {

        return wp_get_current_user()->user_email;
    }

    public function needStrongerCss() {

        /**
         * If Divi plugin installed without Divi theme we must use stronger CSS selectors
         */
        if (!$this->isAdmin() && function_exists('et_is_builder_plugin_active') && et_is_builder_plugin_active()) {
            return true;
        }

        return false;
    }

    public function getDebug() {
        $debug = array('');


        $debug[] = 'Path to uri:';
        $uris    = Url::getUris();
        $paths   = Filesystem::getPaths();
        foreach ($uris as $i => $uri) {
            $debug[] = $paths[$i] . ' => ' . $uri;
        }

        $debug[] = '';
        $debug[] = 'wp_upload_dir() => ';
        foreach (wp_upload_dir() as $k => $v) {
            $debug[] = $k . ': ' . $v;
        }
        $debug[] = '<=';
        $debug[] = '';

        $theme       = wp_get_theme();
        $parentTheme = $theme->parent();
        if ($parentTheme) {
            $debug[] = 'Parent theme: ' . $theme->get('Name') . " is version " . $theme->get('Version');
        }
        $debug[] = 'Theme: ' . $theme->get('Name') . " is version " . $theme->get('Version');


        $plugins   = get_plugins();
        $notActive = array();

        $debug[] = '';
        $debug[] = 'Activated Plugins:';
        foreach ($plugins as $plugin => $pluginData) {
            if (is_plugin_active($plugin)) {
                $debug[] = ' - ' . $plugin . ' - ' . $pluginData['Version'] . ' - ' . $pluginData['Name'];
            } else {
                $notActive[$plugin] = $pluginData;
            }
        }

        $debug[] = '';
        $debug[] = '';
        $debug[] = 'NOT Activated Plugins:';
        foreach ($notActive as $plugin => $pluginData) {
            $debug[] = ' - ' . $plugin . ' - ' . $pluginData['Version'] . ' - ' . $pluginData['Name'];
        }

        return $debug;
    }

    public function isBeaverBuilderActive() {
        return isset($_GET['fl_builder']);
    }
}