<?php

namespace Nextend\SmartSlider3\Application\Admin\Preview;

use Nextend\Framework\Asset\Js\Js;
use Nextend\SmartSlider3\Settings;

/**
 * @var $this ViewPreviewFull
 */

JS::addGlobalInline('document.documentElement.classList.add("n2_html--application-only");');
JS::addGlobalInline('document.documentElement.classList.add("n2_html--slider-preview");');

$slider = $this->renderSlider();

$externals = Settings::get('external-css-files');
if (!empty($externals)) {
    $externals = explode("\n", $externals);
    foreach ($externals as $external) {
        echo "<link rel='stylesheet' href='" . $external . "' type='text/css' media='all' />";
    }
}

echo $slider;


$slidesData = $this->getSlidesData();
if (!empty($slidesData)) {
    $slideId = key($slidesData);
    if ($slideId > 0) {
        ?>
        <script type="text/javascript">
            n2ss.ready(<?php echo $this->getSliderID(); ?>, function (slider) {
                slider.visible(function () {
                    slider.slideToID(<?php echo key($slidesData); ?>);
                });
            });
        </script>
        <?php
    }
}
?>


<script type="text/javascript">

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            parent.postMessage(JSON.stringify({action: 'cancel'}), "*");
        }
    });
    if (window.parent !== window) {
        N2R('documentReady', function ($) {
            var $sliders = n2('.n2-ss-slider');

            function syncDeviceDetails() {
                $sliders.each(function () {
                    var match = $(this).attr('id').match(/n2-ss-([0-9]+)/);
                    if (match) {
                        n2ss.ready(match[1], function (slider) {
                            slider.stages.done('Show', function () {
                                syncDeviceDetailsSlider(slider);
                            });
                        });
                    }
                });
            }

            function syncDeviceDetailsSlider(slider) {
                var breakpoints = slider.responsive.parameters.breakpoints,
                    breakpoint, screenWidthLimit, maxWidth = -1, minWidth = 0, hadMinScreenWidth = false, i;

                for (i = breakpoints.length - 1; i >= 0; i--) {
                    breakpoint = breakpoints[i];
                    screenWidthLimit = slider.responsive.isLandscape ? breakpoint.landscapeWidth : breakpoint.portraitWidth;

                    if (breakpoint.type === 'max-screen-width') {
                        minWidth = maxWidth + 1;
                        maxWidth = screenWidthLimit;
                    } else if (breakpoint.type === 'min-screen-width') {
                        hadMinScreenWidth = true;
                        if (slider.responsive.device === 'desktopPortrait') {
                            maxWidth = screenWidthLimit - 1;
                        } else {
                            minWidth = screenWidthLimit;
                            maxWidth = 100000;
                        }
                    }

                    if (breakpoint.device === slider.responsive.device) {
                        break;
                    }
                }

                if (!hadMinScreenWidth && slider.responsive.device === 'desktopPortrait') {
                    minWidth = screenWidthLimit + 1;
                    maxWidth = 100000;
                }

                window.parent.postMessage(
                    JSON.stringify({
                        action: 'device_info',
                        data: {
                            id: slider.id,
                            top: slider.sliderElement[0].getBoundingClientRect().top + document.documentElement.scrollTop,
                            device: slider.responsive.device,
                            isLandscape: slider.responsive.isLandscape,
                            minScreenWidth: minWidth,
                            maxScreenWidth: maxWidth
                        }
                    }),
                    "*"
                );
            }

            if (window.ResizeObserver !== undefined) {
                var observer = new ResizeObserver((function () {
                    syncDeviceDetails();
                }).bind(this));
                observer.observe(document.body);
            } else {
                try {
                    /**
                     * We can detect every width changes with a dummy iframe.
                     */
                    $('<iframe sandbox="allow-same-origin allow-scripts" style="position:absolute;left:0;top:0;margin:0;padding:0;border:0;display:block;width:100%;height:100%;min-height:0;max-height:none;z-index:10;"></iframe>')
                        .on('load', function (e) {
                            $(e.target.contentWindow ? e.target.contentWindow : e.target.contentDocument.defaultView)
                                .on('resize', function () {
                                    syncDeviceDetails();
                                });
                        })
                        .appendTo(document.body);
                } catch (e) {
                }
            }

            $(document).on('SliderDeviceOrientation', function (e, data) {
                syncDeviceDetailsSlider(data.slider);
            });

            function broadcastScrollTop(scrollTop) {

                window.parent.postMessage(
                    JSON.stringify({
                        action: 'scrollTop',
                        data: {
                            scrollTop: scrollTop
                        }
                    }),
                    "*"
                );
            }

            document.addEventListener('scroll', function () {
                broadcastScrollTop(document.documentElement.scrollTop || document.body.scrollTop);
            }, {
                passive: true,
                capture: true
            });
            broadcastScrollTop(document.documentElement.scrollTop || document.body.scrollTop);
        });
    }

</script>