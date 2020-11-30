<?php

namespace Nextend\Framework\Localization\WordPress;

use Nextend\Framework\Localization\AbstractLocalization;
use function get_locale;
use function get_user_locale;
use function is_admin;

class WordPressLocalization extends AbstractLocalization {


    public function getLocale() {

        return is_admin() && function_exists('\\get_user_locale') ? get_user_locale() : get_locale();
    }
}