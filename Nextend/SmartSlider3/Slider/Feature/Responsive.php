<?php


namespace Nextend\SmartSlider3\Slider\Feature;


use Nextend\Framework\Data\Data;
use Nextend\SmartSlider3\Application\Admin\Settings\ViewSettingsGeneral;
use Nextend\SmartSlider3\Settings;
use Nextend\SmartSlider3\Slider\ResponsiveType\AbstractResponsiveTypeFrontend;
use Nextend\SmartSlider3\Slider\ResponsiveType\ResponsiveTypeFactory;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\SmartSlider3Info;

class Responsive {

    /** @var  Slider */
    public $slider;

    /**
     * @var AbstractResponsiveTypeFrontend
     */
    protected $responsivePlugin;

    protected $hideOnDesktopLandscape = 1;
    protected $hideOnDesktopPortrait = 1;

    protected $hideOnTabletLandscape = 1;
    protected $hideOnTabletPortrait = 1;

    protected $hideOnMobileLandscape = 1;
    protected $hideOnMobilePortrait = 1;

    public $onResizeEnabled = 1;

    public $type = 'auto';

    public $scaleDown = 1;

    public $scaleUp = 1;

    public $forceFull = 0;

    public $forceFullOverflowX = 'body';

    public $forceFullHorizontalSelector = '';

    public $constrainRatio = 1;

    public $minimumHeight = -1;

    public $maximumSlideWidthLandscape = -1;
    public $maximumSlideWidth = 10000;
    public $maximumSlideWidthTabletLandscape = -1;
    public $maximumSlideWidthTablet = -1;
    public $maximumSlideWidthMobileLandscape = -1;
    public $maximumSlideWidthMobile = -1;

    public $sliderHeightBasedOn = 'real';
    public $responsiveDecreaseSliderHeight = 0;

    public $focusUser = 1;

    public $focusEdge = 'auto';

    protected $enabledDevices = array(
        'desktopLandscape' => 0,
        'desktopPortrait'  => 1,
        'tabletLandscape'  => 0,
        'tabletPortrait'   => 1,
        'mobileLandscape'  => 0,
        'mobilePortrait'   => 1
    );

    protected $breakpoints = array();

    protected $sizes = array(
        'desktopPortrait' => array(
            'width'  => 800,
            'height' => 600
        ),
    );

    public function __construct($slider, $features) {

        $this->slider = $slider;

        $this->hideOnDesktopLandscape = !intval($slider->params->get('desktoplandscape', 1));
        $this->hideOnDesktopPortrait  = !intval($slider->params->get('desktopportrait', 1));

        $this->hideOnTabletLandscape = !intval($slider->params->get('tabletlandscape', 1));
        $this->hideOnTabletPortrait  = !intval($slider->params->get('tabletportrait', 1));

        $this->hideOnMobileLandscape = !intval($slider->params->get('mobilelandscape', 1));
        $this->hideOnMobilePortrait  = !intval($slider->params->get('mobileportrait', 1));


        $this->focusUser = intval($slider->params->get('responsiveFocusUser', 1));

        $this->focusEdge = $slider->params->get('responsiveFocusEdge', 'auto');

        $this->responsivePlugin = ResponsiveTypeFactory::createFrontend($slider->params->get('responsive-mode', 'auto'), $this);
        $this->type             = $this->responsivePlugin->getType();
        $this->responsivePlugin->parse($slider->params, $this, $features);

        $this->onResizeEnabled = !$slider->disableResponsive;

        if (!$this->scaleDown && !$this->scaleUp) {
            $this->onResizeEnabled = 0;
        }

        $overrideSizeEnabled = !!$slider->params->get('slider-size-override', 0);

        $this->sizes['desktopPortrait']['width']  = max(10, intval($slider->params->get('width', 1200)));
        $this->sizes['desktopPortrait']['height'] = max(10, intval($slider->params->get('height', 600)));

        $heightHelperRatio = $this->sizes['desktopPortrait']['height'] / $this->sizes['desktopPortrait']['width'];

        $this->enabledDevices['desktopLandscape'] = intval($slider->params->get('responsive-breakpoint-desktop-landscape-enabled', 0));
        $this->enabledDevices['tabletLandscape']  = intval($slider->params->get('responsive-breakpoint-tablet-landscape-enabled', 0));
        $this->enabledDevices['tabletPortrait']   = intval($slider->params->get('responsive-breakpoint-tablet-portrait-enabled', 1));
        $this->enabledDevices['mobileLandscape']  = intval($slider->params->get('responsive-breakpoint-mobile-landscape-enabled', 0));
        $this->enabledDevices['mobilePortrait']   = intval($slider->params->get('responsive-breakpoint-mobile-portrait-enabled', 1));

        $useLocalBreakpoints = !$slider->params->get('responsive-breakpoint-global', 0);

        $landscapePortraitWidth = $breakpointWidthLandscape = 3001;
        $previousSize           = false;

        if ($this->enabledDevices['desktopLandscape']) {

            $landscapePortraitWidth   = $breakpointWidthPortrait = intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-desktop-portrait', ViewSettingsGeneral::defaults['desktop-large-portrait']) : Settings::get('responsive-screen-width-desktop-portrait', ViewSettingsGeneral::defaults['desktop-large-portrait']));
            $breakpointWidthLandscape = max($landscapePortraitWidth, intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-desktop-portrait-landscape', ViewSettingsGeneral::defaults['desktop-large-landscape']) : Settings::get('responsive-screen-width-desktop-portrait-landscape', ViewSettingsGeneral::defaults['desktop-large-landscape'])));

            $this->breakpoints[] = array(
                'device'         => 'desktopLandscape',
                'type'           => 'min-screen-width',
                'portraitWidth'  => $breakpointWidthPortrait,
                'landscapeWidth' => $breakpointWidthLandscape
            );

            $editorWidth = intval($slider->params->get('desktop-landscape-width', 1440));

            if ($overrideSizeEnabled && $slider->params->get('slider-size-override-desktop-landscape', 0) && $editorWidth > 10) {

                $editorHeight = intval($slider->params->get('desktop-landscape-height', 900));

                if ($editorWidth < $breakpointWidthPortrait) {
                    if ($editorHeight > 0) {
                        $editorHeight = $breakpointWidthPortrait / $editorWidth * $editorHeight;
                    }

                    $editorWidth = $breakpointWidthPortrait;
                }

                if ($editorHeight <= 0) {
                    switch ($this->slider->data->get('type', 'simple')) {
                        case 'carousel':
                        case 'showcase':
                            $editorHeight = 0;
                            break;
                        default:
                            $editorHeight = $editorWidth * $heightHelperRatio;
                    }
                }

                $this->sizes['desktopLandscape'] = array(
                    'width'  => $editorWidth,
                    'height' => floor($editorHeight)
                );
            } else {

                $this->sizes['desktopLandscape'] = array(
                    'width'  => $this->sizes['desktopPortrait']['width'],
                    'height' => $this->sizes['desktopPortrait']['height']
                );
            }

            $this->sizes['desktopLandscape']['max'] = 3000;
            $this->sizes['desktopLandscape']['min'] = $breakpointWidthPortrait;

            $previousSize = &$this->sizes['desktopLandscape'];

        }

        $this->sizes['desktopPortrait']['max'] = max($this->sizes['desktopPortrait']['width'], $landscapePortraitWidth - 1, $breakpointWidthLandscape - 1);

        $previousSize = &$this->sizes['desktopPortrait'];

        /**
         * Keep a copy of the current smallest width to be able to disable smaller devices
         */
        $smallestWidth = $this->sizes['desktopPortrait']['width'];

        if ($this->enabledDevices['tabletLandscape']) {

            $breakpointWidthPortrait  = intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-tablet-landscape', ViewSettingsGeneral::defaults['tablet-large-portrait']) : Settings::get('responsive-screen-width-tablet-landscape', ViewSettingsGeneral::defaults['tablet-large-portrait']));
            $breakpointWidthLandscape = max($breakpointWidthPortrait, intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-tablet-landscape-landscape', ViewSettingsGeneral::defaults['tablet-large-landscape']) : Settings::get('responsive-screen-width-tablet-landscape-landscape', ViewSettingsGeneral::defaults['tablet-large-landscape'])));

            $this->breakpoints[] = array(
                'device'         => 'tabletLandscape',
                'type'           => 'max-screen-width',
                'portraitWidth'  => $breakpointWidthPortrait,
                'landscapeWidth' => $breakpointWidthLandscape
            );

            $editorWidth = intval($slider->params->get('tablet-landscape-width', 1024));

            if ($overrideSizeEnabled && $slider->params->get('slider-size-override-tablet-landscape', 0) && $editorWidth > 10) {

                $editorHeight = intval($slider->params->get('tablet-landscape-height', 768));

                if ($editorWidth > $breakpointWidthPortrait) {
                    if ($editorHeight > 0) {
                        $editorHeight = $breakpointWidthPortrait / $editorWidth * $editorHeight;
                    }

                    $editorWidth = $breakpointWidthPortrait;
                }

                if ($editorHeight <= 0) {
                    $editorHeight = $editorWidth * $heightHelperRatio;
                }

                $this->sizes['tabletLandscape'] = array(
                    'width'  => $editorWidth,
                    'height' => floor($editorHeight)
                );

                $smallestWidth = min($smallestWidth, $editorWidth);
            } else {
                $width = min($smallestWidth, $breakpointWidthPortrait);

                $this->sizes['tabletLandscape'] = array(
                    'width'  => $width,
                    'height' => floor($width * $heightHelperRatio),
                    'auto'   => true
                );

                $smallestWidth = min($smallestWidth, $breakpointWidthPortrait);
            }

            $this->sizes['tabletLandscape']['max'] = max($this->sizes['tabletLandscape']['width'], $breakpointWidthPortrait, $breakpointWidthLandscape);

            $previousSize['min'] = min($previousSize['width'], $breakpointWidthPortrait + 1);

            $previousSize = &$this->sizes['tabletLandscape'];

        }

        if ($this->enabledDevices['tabletPortrait']) {

            $breakpointWidthPortrait  = intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-tablet-portrait', ViewSettingsGeneral::defaults['tablet-portrait']) : Settings::get('responsive-screen-width-tablet-portrait', ViewSettingsGeneral::defaults['tablet-portrait']));
            $breakpointWidthLandscape = max($breakpointWidthPortrait, intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-tablet-portrait-landscape', ViewSettingsGeneral::defaults['tablet-landscape']) : Settings::get('responsive-screen-width-tablet-portrait-landscape', ViewSettingsGeneral::defaults['tablet-landscape'])));

            $this->breakpoints[] = array(
                'device'         => 'tabletPortrait',
                'type'           => 'max-screen-width',
                'portraitWidth'  => $breakpointWidthPortrait,
                'landscapeWidth' => $breakpointWidthLandscape
            );

            $editorWidth = intval($slider->params->get('tablet-portrait-width', 768));

            if ($overrideSizeEnabled && $slider->params->get('slider-size-override-tablet-portrait', 0) && $editorWidth > 10) {

                $editorHeight = intval($slider->params->get('tablet-portrait-height', 1024));

                if ($editorWidth > $breakpointWidthPortrait) {
                    if ($editorHeight > 0) {
                        $editorHeight = $breakpointWidthPortrait / $editorWidth * $editorHeight;
                    }

                    $editorWidth = $breakpointWidthPortrait;
                }

                if ($editorHeight <= 0) {
                    $editorHeight = $editorWidth * $heightHelperRatio;
                }

                $this->sizes['tabletPortrait'] = array(
                    'width'  => $editorWidth,
                    'height' => floor($editorHeight)
                );

                $smallestWidth = min($smallestWidth, $editorWidth);
            } else {
                $width = min($smallestWidth, $breakpointWidthPortrait);

                $this->sizes['tabletPortrait'] = array(
                    'width'  => $width,
                    'height' => floor($width * $heightHelperRatio),
                    'auto'   => true
                );

                $smallestWidth = min($smallestWidth, $breakpointWidthPortrait);
            }

            $this->sizes['tabletPortrait']['max'] = max($this->sizes['tabletPortrait']['width'], $breakpointWidthPortrait, $breakpointWidthLandscape);

            $previousSize['min'] = min($previousSize['width'], $breakpointWidthPortrait + 1);

            $previousSize = &$this->sizes['tabletPortrait'];
        }

        if ($this->enabledDevices['mobileLandscape']) {

            $breakpointWidthPortrait  = intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-mobile-landscape', ViewSettingsGeneral::defaults['mobile-large-portrait']) : Settings::get('responsive-screen-width-mobile-landscape', ViewSettingsGeneral::defaults['mobile-large-portrait']));
            $breakpointWidthLandscape = max($breakpointWidthPortrait, intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-mobile-landscape-landscape', ViewSettingsGeneral::defaults['mobile-large-landscape']) : Settings::get('responsive-screen-width-mobile-landscape-landscape', ViewSettingsGeneral::defaults['mobile-large-landscape'])));

            $this->breakpoints[] = array(
                'device'         => 'mobileLandscape',
                'type'           => 'max-screen-width',
                'portraitWidth'  => $breakpointWidthPortrait,
                'landscapeWidth' => $breakpointWidthLandscape
            );


            $editorWidth = intval($slider->params->get('mobile-landscape-width', 568));

            if ($overrideSizeEnabled && $slider->params->get('slider-size-override-mobile-landscape', 0) && $editorWidth > 10) {

                $editorHeight = intval($slider->params->get('mobile-landscape-height', 320));

                if ($editorWidth > $breakpointWidthPortrait) {
                    if ($editorHeight > 0) {
                        $editorHeight = $breakpointWidthPortrait / $editorWidth * $editorHeight;
                    }

                    $editorWidth = $breakpointWidthPortrait;
                }

                if ($editorHeight <= 0) {
                    $editorHeight = $editorWidth * $heightHelperRatio;
                }

                $this->sizes['mobileLandscape'] = array(
                    'width'  => $editorWidth,
                    'height' => floor($editorHeight)
                );

                $smallestWidth = min($smallestWidth, $editorWidth);
            } else {

                $width = min($smallestWidth, $breakpointWidthPortrait);

                $this->sizes['mobileLandscape'] = array(
                    'width'  => $width,
                    'height' => floor($width * $heightHelperRatio),
                    'auto'   => true
                );

                $smallestWidth = min($smallestWidth, $breakpointWidthPortrait);
            }

            $this->sizes['mobileLandscape']['max'] = max($this->sizes['mobileLandscape']['width'], $breakpointWidthPortrait, $breakpointWidthLandscape);

            $previousSize['min'] = min($previousSize['width'], $breakpointWidthPortrait + 1);

            $previousSize = &$this->sizes['mobileLandscape'];
        }

        if ($this->enabledDevices['mobilePortrait']) {

            $breakpointWidthPortrait  = intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-mobile-portrait', ViewSettingsGeneral::defaults['mobile-portrait']) : Settings::get('responsive-screen-width-mobile-portrait', ViewSettingsGeneral::defaults['mobile-portrait']));
            $breakpointWidthLandscape = max($breakpointWidthPortrait, intval($useLocalBreakpoints ? $slider->params->get('responsive-breakpoint-mobile-portrait-landscape', ViewSettingsGeneral::defaults['mobile-landscape']) : Settings::get('responsive-screen-width-mobile-portrait-landscape', ViewSettingsGeneral::defaults['mobile-landscape'])));

            $this->breakpoints[] = array(
                'device'         => 'mobilePortrait',
                'type'           => 'max-screen-width',
                'portraitWidth'  => $breakpointWidthPortrait,
                'landscapeWidth' => $breakpointWidthLandscape
            );


            $editorWidth = intval($slider->params->get('mobile-portrait-width', 320));

            if ($overrideSizeEnabled && $slider->params->get('slider-size-override-mobile-portrait', 0) && $editorWidth > 10) {
                $editorHeight = intval($slider->params->get('mobile-portrait-height', 568));

                if ($editorWidth > $breakpointWidthPortrait) {
                    if ($editorHeight > 0) {
                        $editorHeight = $breakpointWidthPortrait / $editorWidth * $editorHeight;
                    }

                    $editorWidth = $breakpointWidthPortrait;
                }

                if ($editorHeight <= 0) {
                    $editorHeight = $editorWidth * $heightHelperRatio;
                }

                $this->sizes['mobilePortrait'] = array(
                    'width'  => $editorWidth,
                    'height' => floor($editorHeight)
                );
            } else {
                $width = min(320, $smallestWidth, $breakpointWidthPortrait);

                $this->sizes['mobilePortrait'] = array(
                    'width'  => $width,
                    'height' => floor($width * $heightHelperRatio)
                );
            }

            $this->sizes['mobilePortrait']['max'] = max($this->sizes['mobilePortrait']['width'], $breakpointWidthPortrait, $breakpointWidthLandscape);

            $previousSize['min'] = min($previousSize['width'], $breakpointWidthPortrait + 1);

            $previousSize = &$this->sizes['mobilePortrait'];
        }

        $previousSize['min'] = min(320, $previousSize['width']);

        if (isset($this->sizes['mobileLandscape']['auto'])) {
            unset($this->sizes['mobileLandscape']['auto']);

            $this->sizes['mobileLandscape']['width']  = $this->sizes['mobileLandscape']['min'];
            $this->sizes['mobileLandscape']['height'] = floor($this->sizes['mobileLandscape']['width'] * $heightHelperRatio);
        }

        if (isset($this->sizes['tabletPortrait']['auto'])) {
            unset($this->sizes['tabletPortrait']['auto']);

            $this->sizes['tabletPortrait']['width']  = $this->sizes['tabletPortrait']['min'];
            $this->sizes['tabletPortrait']['height'] = floor($this->sizes['tabletPortrait']['width'] * $heightHelperRatio);
        }

        if (isset($this->sizes['tabletLandscape']['auto'])) {
            unset($this->sizes['tabletLandscape']['auto']);

            $this->sizes['tabletLandscape']['width']  = $this->sizes['tabletLandscape']['min'];
            $this->sizes['tabletLandscape']['height'] = floor($this->sizes['tabletLandscape']['width'] * $heightHelperRatio);
        }

        $this->parseLimitSlideWidth($slider->params);
    }

    public function makeJavaScriptProperties(&$properties) {
        $normalizedDeviceModes = array(
            'unknown'         => 'desktopPortrait',
            'desktopPortrait' => 'desktopPortrait'
        );

        if (!$this->enabledDevices['desktopLandscape']) {
            $normalizedDeviceModes['desktopLandscape'] = $normalizedDeviceModes['desktopPortrait'];
        } else {
            $normalizedDeviceModes['desktopLandscape'] = 'desktopLandscape';
        }


        if (!$this->enabledDevices['tabletLandscape']) {
            $normalizedDeviceModes['tabletLandscape'] = $normalizedDeviceModes['desktopPortrait'];
        } else {
            $normalizedDeviceModes['tabletLandscape'] = 'tabletLandscape';
        }

        if (!$this->enabledDevices['tabletPortrait']) {
            $normalizedDeviceModes['tabletPortrait'] = $normalizedDeviceModes['tabletLandscape'];
        } else {
            $normalizedDeviceModes['tabletPortrait'] = 'tabletPortrait';
        }


        if (!$this->enabledDevices['mobileLandscape']) {
            $normalizedDeviceModes['mobileLandscape'] = $normalizedDeviceModes['tabletPortrait'];
        } else {
            $normalizedDeviceModes['mobileLandscape'] = 'mobileLandscape';
        }

        if (!$this->enabledDevices['mobilePortrait']) {
            $normalizedDeviceModes['mobilePortrait'] = $normalizedDeviceModes['mobileLandscape'];
        } else {
            $normalizedDeviceModes['mobilePortrait'] = 'mobilePortrait';
        }

        if ($this->maximumSlideWidthLandscape <= 0) {
            $this->maximumSlideWidthLandscape = $this->maximumSlideWidth;
        }

        if ($this->maximumSlideWidthTablet <= 0) {
            $this->maximumSlideWidthTablet = $this->maximumSlideWidth;
        }

        if ($this->maximumSlideWidthTabletLandscape <= 0) {
            $this->maximumSlideWidthTabletLandscape = $this->maximumSlideWidthTablet;
        }

        if ($this->maximumSlideWidthMobile <= 0) {
            $this->maximumSlideWidthMobile = $this->maximumSlideWidth;
        }

        if ($this->maximumSlideWidthMobileLandscape <= 0) {
            $this->maximumSlideWidthMobileLandscape = $this->maximumSlideWidthMobile;
        }

        $properties['responsive'] = array(
            'hideOn' => array(
                'desktopLandscape' => $this->hideOnDesktopLandscape,
                'desktopPortrait'  => SmartSlider3Info::$forceDesktop ? false : $this->hideOnDesktopPortrait,
                'tabletLandscape'  => $this->hideOnTabletLandscape,
                'tabletPortrait'   => $this->hideOnTabletPortrait,
                'mobileLandscape'  => $this->hideOnMobileLandscape,
                'mobilePortrait'   => $this->hideOnMobilePortrait,
            ),

            'onResizeEnabled'             => $this->onResizeEnabled,
            'type'                        => $this->type,
            'downscale'                   => $this->scaleDown,
            'upscale'                     => $this->scaleUp,
            'minimumHeight'               => $this->minimumHeight,
            'maximumSlideWidth'           => array(
                'desktopLandscape' => $this->maximumSlideWidthLandscape,
                'desktopPortrait'  => $this->maximumSlideWidth,
                'tabletLandscape'  => $this->maximumSlideWidthTabletLandscape,
                'tabletPortrait'   => $this->maximumSlideWidthTablet,
                'mobileLandscape'  => $this->maximumSlideWidthMobileLandscape,
                'mobilePortrait'   => $this->maximumSlideWidthMobile
            ),
            'forceFull'                   => $this->forceFull,
            'forceFullOverflowX'          => $this->forceFullOverflowX,
            'forceFullHorizontalSelector' => $this->forceFullHorizontalSelector,
            'constrainRatio'              => $this->constrainRatio,
            'sliderHeightBasedOn'         => $this->sliderHeightBasedOn,
            'decreaseSliderHeight'        => $this->responsiveDecreaseSliderHeight,

            'focusUser' => $this->focusUser,
            'focusEdge' => $this->focusEdge,

            'breakpoints'    => $this->breakpoints,
            'enabledDevices' => $this->enabledDevices,
            'sizes'          => $this->sizes,

            'normalizedDeviceModes' => $normalizedDeviceModes,

            'overflowHiddenPage' => intval($this->slider->params->get('overflow-hidden-page', 0))
        );
    }

    /**
     * @param Data $params
     */
    private function parseLimitSlideWidth($params) {
        if ($params->get('responsiveLimitSlideWidth', 1)) {

            if ($this->enabledDevices['desktopLandscape']) {
                if ($params->get('responsiveSlideWidthDesktopLandscape', 0)) {
                    $this->maximumSlideWidthLandscape = intval($params->get('responsiveSlideWidthMaxDesktopLandscape', 1600));
                }
            }

            if ($params->get('responsiveSlideWidth', 0)) {
                $this->maximumSlideWidth = intval($params->get('responsiveSlideWidthMax', 3000));
            } else {
                $this->maximumSlideWidth = $this->sizes['desktopPortrait']['width'];
            }

            if ($this->maximumSlideWidth < 1) {
                $this->maximumSlideWidth = 10000;
            }


            if ($this->enabledDevices['desktopLandscape']) {
                if ($params->get('responsiveSlideWidthTabletLandscape', 0)) {
                    $this->maximumSlideWidthTabletLandscape = intval($params->get('responsiveSlideWidthMaxTabletLandscape', 1200));
                }
            }

            if ($params->get('responsiveSlideWidthTablet', 0)) {
                $this->maximumSlideWidthTablet = intval($params->get('responsiveSlideWidthMaxTablet', 980));
            }


            if ($this->enabledDevices['desktopLandscape']) {
                if ($params->get('responsiveSlideWidthMobileLandscape', 0)) {
                    $this->maximumSlideWidthMobileLandscape = intval($params->get('responsiveSlideWidthMaxMobileLandscape', 780));
                }
            }

            if ($params->get('responsiveSlideWidthMobile', 0)) {
                $this->maximumSlideWidthMobile = intval($params->get('responsiveSlideWidthMaxMobile', 480));
            }
        }
    }
}