<?php

namespace Nextend\Framework\Localization;

use MO;
use Nextend\Framework\Localization\Joomla\JoomlaLocalization;
use Nextend\Framework\Localization\WordPress\WordPressLocalization;
use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\Settings;
use NOOP_Translations;

class Localization {

    use SingletonTrait;

    /**
     * @var AbstractLocalization
     */
    private static $platformLocalization;

    private static $l10n = array();

    private static $js = array();

    protected function init() {

        require_once 'Pomo/translations.php';
        require_once 'Pomo/mo.php';
        require_once 'Functions.php';
        self::$platformLocalization = new WordPressLocalization();
    }

    public static function getLocale() {
        return self::$platformLocalization->getLocale();
    }

    private static function loadTextDomain($domain, $mofile) {
        if (!is_readable($mofile)) return false;

        $mo = new MO();
        if (!$mo->import_from_file($mofile)) return false;

        if (isset(self::$l10n[$domain])) $mo->merge_with(self::$l10n[$domain]);
        self::$l10n[$domain] = &$mo;

        return true;
    }

    public static function loadPluginTextDomain($path, $domain = 'nextend') {
        if (Platform::isAdmin() && Settings::get('force-english-backend')) {
            $locale = 'en_EN';
        } else {
            $locale = self::getLocale();
        }
        $mofile = $locale . '.mo';
        if ($loaded = self::loadTextDomain($domain, $path . '/' . $mofile)) {
            return $loaded;
        }

        return false;
    }

    public static function getTranslationsForDomain($domain) {
        if (!isset(self::$l10n[$domain])) {
            self::$l10n[$domain] = new NOOP_Translations;
        }

        return self::$l10n[$domain];
    }

    public static function addJS($texts) {
        foreach ((array)$texts as $text) {
            self::$js[$text] = n2_($text);
        }
    }

    public static function toJS() {
        if (count(self::$js)) {
            return 'window.nextend.localization = ' . json_encode(self::$js) . ';';
        }

        return '';
    }
}