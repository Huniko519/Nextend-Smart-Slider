<?php

namespace Nextend\SmartSlider3\Application\Frontend\Slider;

use Nextend\Framework\Asset\AssetManager;
use Nextend\SmartSlider3\Settings;
use Nextend\WordPress\OutputBuffer;

/**
 * @var ViewIframe $this
 */

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex">
    <title>Slider</title>
    <style>
        html, body {
            overflow: hidden;
        }

        body * {
            background-attachment: scroll !important;
        }
    </style>
    <?php
    /**
     * In page builder -> editors, we must force sliders to be visible on every device.
     */
    if (isset($_GET['iseditor']) && $_GET['iseditor']):
        ?>
        <script>
            window.ssOverrideHideOn = {
                desktopLandscape: 0,
                desktopPortrait: 0,
                tabletLandscape: 0,
                tabletPortrait: 0,
                mobileLandscape: 0,
                mobilePortrait: 0
            };
        </script>
    <?php
    endif;
    ?>

    <?php


    $handlers = ob_list_handlers();
    if (!in_array(OutputBuffer::class . '::outputCallback', $handlers)) {
        if (class_exists('\\Nextend\\Framework\\Asset\\AssetManager', false)) {
            echo AssetManager::getCSS();
            echo AssetManager::getJs();
        }
    }

    $externals = Settings::get('external-css-files');
    if (!empty($externals)) {
        $externals = explode("\n", $externals);
        foreach ($externals as $external) {
            echo "<link rel='stylesheet' href='" . $external . "' type='text/css' media='all' />";
        }
    }
    ?>
</head>
<body>
<?php
echo $this->getSliderHTML();
?>
<script>
    var tmpChange,
        notifyParentAboutChange = function (e, responsive) {
            tmpChange = [e, responsive];
        };

    function broadCastReady() {
        parent.postMessage({key: 'ready'}, "*");
    }

    N2R('windowLoad', function ($) {

        <?php
        if($this->isGroup()){
        ?>
        var deferreds = [],
            interval = setInterval(broadCastReady, 40),
            hasForceFull = false;

        function initSliders() {
            for (var k in n2ss.sliders) {
                var deferred = $.Deferred();
                deferreds.push(deferred);

                n2ss.ready(k, (function (deferred, slider) {
                    deferred.resolve();

                    var $slider = slider.sliderElement.on({
                        SliderResize: function (e, ratios, responsive) {
                            notifyParentAboutChange(e, responsive);
                        },
                        Show: function (e) {
                            notifyParentAboutChange(e, $slider.data('ss').responsive);
                        }
                    });
                }).bind(this, deferred));
            }
            if (deferreds.length === 0) {
                setTimeout(initSliders, 1000);
            } else {
                $.when(deferreds).done(function () {
                    var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
                    window[eventMethod](eventMethod == "attachEvent" ? "onmessage" : "message", function (e) {
                        var data = e[e.message ? "message" : "data"];
                        switch (data["key"]) {
                            case "ackReady":

                                window.n2Width = data.windowInnerWidth;
                                window.n2Height = data.windowInnerHeight;
                                window.n2ClientHeight = data.windowInnerHeight;
                                clearInterval(interval);
                                notifyParentAboutChange = NextendDeBounce(function (e, responsive) {
                                    if (!hasForceFull && responsive.parameters.forceFull) {
                                        hasForceFull = true;
                                    }

                                    parent.postMessage({
                                        key: 'resize',
                                        width: $('body').width(),
                                        height: $('body').height(),
                                        forceFull: hasForceFull,
                                        fullPage: 0,
                                        focus: {},
                                        margin: 0
                                    }, "*");
                                }, 33);
                                if (typeof tmpChange !== 'undefined') {
                                    notifyParentAboutChange.apply(this, tmpChange);
                                }
                                $(window).trigger('resize');
                                break;
                            case 'clientHeight':
                                window.n2ClientHeight = data.clientHeight;
                                $(window).trigger('resize');
                                break;
                            case 'windowSize':
                                window.n2Width = data.windowInnerWidth;
                                window.n2Height = data.windowInnerHeight;
                                $(window).trigger('resize');
                                break;
                        }
                    });
                });
            }
        }

        initSliders();
        <?php
        }else{
        ?>
        if (typeof n2ss !== "undefined") {
            n2ss.ready(<?php echo $this->getSliderID(); ?>, function (slider) {

                var $slider = slider.sliderElement.on({
                        SliderResize: function (e, ratios, responsive) {
                            notifyParentAboutChange(e, responsive);
                        },
                        Show: function (e) {
                            notifyParentAboutChange(e, $slider.data('ss').responsive);
                        }
                    }),
                    $margin = $slider.closest('.n2-ss-margin'),
                    margin = [$margin.css('marginTop'), $margin.css('marginRight'), $margin.css('marginBottom'), $margin.css('marginLeft')].join(' ');

                $margin.css('margin', 0);

                // If the slider is already ready, then SliderResize might not happen to adjust the iframe size in the parent
                if (slider && slider.stages.resolved('ResizeFirst')) {
                    notifyParentAboutChange(null, slider.responsive);
                }

                var interval = setInterval(broadCastReady, 40);

                var eventMethod = window.addEventListener ? "addEventListener" : "attachEvent";
                window[eventMethod](eventMethod == "attachEvent" ? "onmessage" : "message", function (e) {
                    var data = e[e.message ? "message" : "data"];
                    switch (data["key"]) {
                        case "ackReady":
                            window.n2Width = data.windowInnerWidth;
                            window.n2Height = data.windowInnerHeight;
                            window.n2ClientHeight = data.windowInnerHeight;
                            clearInterval(interval);
                            notifyParentAboutChange = NextendDeBounce(function (e, responsive) {
                                parent.postMessage({
                                    key: 'resize',
                                    width: $('body').width(),
                                    height: $('body').height(),
                                    forceFull: responsive.parameters.forceFull,
                                    fullPage: responsive.parameters.type === 'fullpage',
                                    focus: responsive.parameters.focus,
                                    margin: margin
                                }, "*");
                            }, 33);
                            if (typeof tmpChange !== 'undefined') {
                                notifyParentAboutChange.apply(this, tmpChange);
                            }
                            break;
                        case 'clientHeight':
                            window.n2ClientHeight = data.clientHeight;
                            $(window).trigger('resize');
                            break;
                        case 'windowSize':
                            window.n2Width = data.windowInnerWidth;
                            window.n2Height = data.windowInnerHeight;
                            $(window).trigger('resize');
                            break;
                    }
                });

                n2const.setLocation = function (l) {
                    parent.postMessage({
                        key: 'setLocation',
                        location: l
                    }, "*");
                };

                slider.stages.done('HasDimension', function () {

                    $('a').each(function () {
                        if ($(this).attr('target') !== '_blank') {
                            $(this).attr('target', '_parent');
                        }
                    });
                });
            });
        } else {
            console.error('This slider has no slides: <?php echo $this->getSliderID(); ?>');
        }
        <?php
        }
        ?>
    });
</script>
</body>
</html>


