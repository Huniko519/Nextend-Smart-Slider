<?php

use Nextend\SmartSlider3\Platform\SmartSlider3Platform;

add_action('plugins_loaded', 'smart_slider_3_pro_plugins_loaded', 20);

function smart_slider_3_pro_plugins_loaded() {

    //Do not load the free version when pro is available
    remove_action('plugins_loaded', 'smart_slider_3_plugins_loaded', 30);

    define('NEXTEND_SMARTSLIDER_3', dirname(__FILE__) . DIRECTORY_SEPARATOR);
    define('NEXTEND_SMARTSLIDER_3_BASENAME', NEXTEND_SMARTSLIDER_3_PRO_BASENAME);
    define('NEXTEND_SMARTSLIDER_3_SLUG', NEXTEND_SMARTSLIDER_3_PRO_SLUG);

    require_once dirname(__FILE__) . '/Defines.php';
    require_once(SMARTSLIDER3_LIBRARY_PATH . '/Autoloader.php');

    SmartSlider3Platform::getInstance();
}