<?php

namespace Nextend\Framework\Icon;

use Nextend\Framework\Asset\AssetManager;
use Nextend\Framework\Asset\Css\Css;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Settings;
use Nextend\Framework\Url\Url;

class Icon {

    public static $icons = array();

    public static $keys = array();

    public static function init() {

        $path      = ResourceTranslator::toPath('$ss3-pro-frontend$/icons/');
        $iconPacks = Filesystem::folders($path);

        foreach ($iconPacks as $iconPack) {
            $manifestPath = $path . $iconPack . '/manifest.json';
            if (Filesystem::fileexists($manifestPath)) {
                self::$icons[$iconPack] = json_decode(Filesystem::readFile($manifestPath), true);


                self::$icons[$iconPack]['path'] = $path . $iconPack . '/dist/' . $iconPack . '.min.css';
                self::$icons[$iconPack]['css']  = Url::pathToUri($path . $iconPack . '/dist/' . $iconPack . '.min.css', false);

                self::$keys[self::$icons[$iconPack]['id']] = &self::$icons[$iconPack];
            }
        }
    }

    public static function serveAdmin() {
        static $isServed = false;
        if (!$isServed) {
            Js::addInline('new N2Classes.Icons(' . json_encode(self::$icons) . ');');
            $isServed = true;
        }
    }

    public static function render($key) {
        $parts = explode(':', $key);
        if (count($parts) != 2) {
            return false;
        }

        $id   = $parts[0];
        $icon = $parts[1];
        if (!isset(self::$keys[$id])) {
            return false;
        }

        $iconPack = &self::$keys[$id];
        if (!isset($iconPack['data'][$icon])) {
            return false;
        }

        if (!AssetManager::$stateStorage->get('icon-' . $iconPack['id'] . '-loaded', 0)) {
            AssetManager::$stateStorage->set('icon-' . $iconPack['id'] . '-loaded', 1);

            if (Platform::isAdmin() || Settings::get('icon-' . $iconPack['id'], 1)) {
                Css::addStaticGroup($iconPack['path'], $iconPack['id']);
            } else if (isset($iconPack['compatibility'])) {
                Css::addInline($iconPack['compatibility']);

                if ($iconPack['id'] == 'fa') {
                    $iconPack['class']  = 'fa';
                    $iconPack['prefix'] = 'fa-';
                }
            }
        }

        if ($iconPack['isLigature']) {

            return array(
                "class"    => $iconPack['class'],
                "ligature" => $icon
            );

        } else {

            return array(
                "class"    => $iconPack['class'] . " " . $iconPack['prefix'] . $icon,
                "ligature" => ""
            );
        }

    }
}

Icon::init();