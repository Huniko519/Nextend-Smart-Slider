<?php

if (!function_exists('smartslider3_fail_php_version')) {
    function smartslider3_fail_php_version() {
        $html_message = sprintf('<div class="error">%s</div>', wpautop(sprintf('Smart Slider 3 requires PHP version 7.0+, plugin is currently NOT RUNNING. Current PHP version: %s', PHP_VERSION)));
        echo wp_kses_post($html_message);
    }
}

if (!function_exists('smartslider3_fail_wp_version')) {
    function smartslider3_fail_wp_version() {
        $html_message = sprintf('<div class="error">%s</div>', wpautop('Smart Slider 3 requires WordPress version 4.9+. Because you are using an earlier version, the plugin is currently NOT RUNNING.'));
        echo wp_kses_post($html_message);
    }
}