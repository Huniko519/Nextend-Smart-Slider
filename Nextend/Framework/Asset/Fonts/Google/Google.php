<?php


namespace Nextend\Framework\Asset\Fonts\Google;


use Nextend\Framework\Asset\AssetManager;

class Google {

    public static $enabled = false;

    public static function addSubset($subset = 'latin') {
        AssetManager::$googleFonts->addSubset($subset);
    }

    public static function addFont($family, $style = '400') {
        AssetManager::$googleFonts->addFont($family, $style);
    }

    public static function build() {
        if (self::$enabled) {
            AssetManager::$googleFonts->loadFonts();
        }
    }
}