<?php


namespace Nextend\Framework\Asset\Fonts\Google;


use Nextend\Framework\Asset\AbstractAsset;
use Nextend\Framework\Asset\Js\Js;
use Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend;

class Asset extends AbstractAsset {

    public static $hasWebFontLoader = false;

    public function getLoadedFamilies() {
        return array_keys($this->files);
    }

    function addSubset($subset = 'latin') {
        if (!in_array($subset, $this->inline)) {
            $this->inline[] = $subset;
        }
    }

    function addFont($family, $style = '400') {
        $style = (string)$style;
        if (!isset($this->files[$family])) {
            $this->files[$family] = array();
        }
        if (!in_array($style, $this->files[$family])) {
            $this->files[$family][] = $style;
        }
    }

    public function loadFonts() {
        $familyQuery = array();
        $names       = array();
        if (count($this->files)) {
            foreach ($this->files AS $family => $styles) {
                if (count($styles)) {
                    $familyQuery[] = $family . ':' . implode(',', $styles);
                    foreach ($styles AS $style) {
                        $names[] = $family . ':' . (substr($style, -6) == 'italic' ? 'i' : 'n') . $style[0];
                    }
                }
            }
        }
        if (empty($familyQuery)) {
            return false;
        }
        $subsets                              = array_unique($this->inline);
        $familyQuery[count($familyQuery) - 1] .= ':' . implode(',', $subsets);

        self::$hasWebFontLoader = true;

        Js::addStaticGroup(ApplicationTypeFrontend::getAssetsPath() . "/dist/nextend-webfontloader.min.js", "nextend-webfontloader");

        Js::addGlobalInline("
        nextend.fontsLoaded = false;
        nextend.fontsLoadedActive = function () {nextend.fontsLoaded = true;};
        var requiredFonts = " . json_encode($names) . ",
            fontData = {
                google: {
                    families: " . json_encode($familyQuery) . "
                },
                active: function(){nextend.fontsLoadedActive()},
                inactive: function(){nextend.fontsLoadedActive()},
                fontactive: function(f,s){fontData.resolveFont(f+':'+s);},
                fontinactive: function(f,s){fontData.resolveFont(f+':'+s);},
                resolveFont: function(n){
                    for(var i = requiredFonts.length - 1; i >= 0; i--) {
                        if(requiredFonts[i] === n) {
                           requiredFonts.splice(i, 1);
                           break;
                        }
                    }
                    if(!requiredFonts.length) nextend.fontsLoadedActive();
                }
            };
        if(typeof WebFontConfig !== 'undefined' && typeof WebFont === 'undefined'){
            var _WebFontConfig = WebFontConfig;
            for(var k in WebFontConfig){
                if(k == 'active'){
                  fontData.active = function(){nextend.fontsLoadedActive();_WebFontConfig.active();}
                }else if(k == 'inactive'){
                  fontData.inactive = function(){nextend.fontsLoadedActive();_WebFontConfig.inactive();}
                }else if(k == 'fontactive'){
                  fontData.fontactive = function(f,s){fontData.resolveFont(f+':'+s);_WebFontConfig.fontactive.apply(this,arguments);}
                }else if(k == 'fontinactive'){
                  fontData.fontinactive = function(f,s){fontData.resolveFont(f+':'+s);_WebFontConfig.fontinactive.apply(this,arguments);}
                }else if(k == 'google'){
                    if(typeof WebFontConfig.google.families !== 'undefined'){
                        for(var i = 0; i < WebFontConfig.google.families.length; i++){
                            fontData.google.families.push(WebFontConfig.google.families[i]);
                        }
                    }
                }else{
                    fontData[k] = WebFontConfig[k];
                }
            }
        }
        fontData.classes=true;
        fontData.events=true;
        
        if(typeof WebFont === 'undefined'){
            window.WebFontConfig = fontData;
        }else{
            WebFont.load(fontData);
        }");

        Js::addFirstCode("
        nextend.fontsDeferred = $.Deferred();
        if(nextend.fontsLoaded){
            nextend.fontsDeferred.resolve();
        }else{
            nextend.fontsLoadedActive = function () {
                nextend.fontsLoaded = true;
                nextend.fontsDeferred.resolve();
            };
            var intercalCounter = 0;
            nextend.fontInterval = setInterval(function(){
                if(intercalCounter > 3 || document.documentElement.className.indexOf('wf-active') !== -1){
                    nextend.fontsLoadedActive();
                    clearInterval(nextend.fontInterval);
                }
                intercalCounter++;
            }, 1000);
        }", true);

        return true;
    }
}