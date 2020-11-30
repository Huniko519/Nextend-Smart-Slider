<?php

namespace Nextend\Framework\Asset;


use Nextend\Framework\Asset\Builder\BuilderJs;
use Nextend\Framework\Asset\Css\Css;
use Nextend\Framework\Asset\Fonts\Google\Google;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Font\FontSources;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\Plugin;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Url\Url;
use Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend;
use Nextend\SmartSlider3\Settings;

class Predefined {

    public static function backend($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once   = true;
        $family = n2_x('Montserrat', 'Default Google font family for admin');
        foreach (explode(',', n2_x('latin', 'Default Google font charset for admin')) as $subset) {
            Google::addSubset($subset);
        }
        Google::addFont($family);

        Js::addFirstCode("N2R(['AjaxHelper'],function(){N2Classes.AjaxHelper.addAjaxArray(" . json_encode(Form::tokenizeUrl()) . ");});");

        Plugin::addAction('afterApplicationContent', array(
            FontSources::class,
            'onFontManagerLoadBackend'
        ));
    }

    public static function frontend($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once = true;
        AssetManager::getInstance();
        if (Platform::isAdmin()) {
            Js::addGlobalInline('window.N2GSAP=' . N2GSAP . ';');
            Js::addGlobalInline('window.N2PLATFORM="' . Platform::getName() . '";');
        }
    

        Js::addGlobalInline('(function(){var N=this;N.N2_=N.N2_||{r:[],d:[]},N.N2R=N.N2R||function(){N.N2_.r.push(arguments)},N.N2D=N.N2D||function(){N.N2_.d.push(arguments)}}).call(window);');
        Js::addGlobalInline('if(!window.n2jQuery){window.n2jQuery={ready:function(cb){console.error(\'n2jQuery will be deprecated!\');N2R([\'$\'],cb);}}}');
        $jQueryFallback = site_url('wp-includes/js/jquery/jquery.js');

        Js::addGlobalInline('window.nextend={jQueryFallback:\'' . $jQueryFallback . '\',localization: {}, ready: function(cb){console.error(\'nextend.ready will be deprecated!\');N2R(\'documentReady\', function($){cb.call(window,$)})}};');

        Js::jQuery($force);


        self::animation($force);

        FontSources::onFontManagerLoad($force);
    }

    private static function animation($force = false) {
        static $once;
        if ($once != null && !$force) {
            return;
        }
        $once = true;
        $gsapEnabled = \Nextend\Framework\Settings::get('gsap', 1);
        if (($gsapEnabled !== '0' && $gsapEnabled !== 0) || Platform::isAdmin()) {

            Js::addStaticGroup(ApplicationTypeFrontend::getAssetsPath() . "/dist/nextend-gsap.min.js", 'nextend-gsap');

        } else {

            Js::addGlobalInline('window.NextendGSAPFallback=' . json_encode(Url::pathToUri(ApplicationTypeFrontend::getAssetsPath() . "/dist/nextend-gsap.min.js")) . ';');
            Js::addInline(Filesystem::readFile(ApplicationTypeFrontend::getAssetsPath() . "/dist/nextend-gsap-external.min.js"));

        }
    
    }

    public static function loadLiteBox() {

        Css::addStaticGroup(ResourceTranslator::toPath('$ss3-pro-frontend$/dist/litebox.min.css'), 'litebox');

        Js::addStaticGroup(ResourceTranslator::toPath('$ss3-pro-frontend$/dist/litebox.min.js'), 'litebox');

        Js::addInline('n2const.lightboxMobileNewTab=' . intval(Settings::get('lightbox-mobile-new-tab', 1)) . ';');
    
    }
}