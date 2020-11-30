<?php


namespace Nextend\SmartSlider3\Slider\SliderType;


use Nextend\Framework\Asset\Builder\BuilderJs;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\Frontend\ApplicationTypeFrontend;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\Widget\SliderWidget;

abstract class AbstractSliderTypeFrontend {

    /**
     * @var Slider
     */
    protected $slider;

    protected $jsDependency = array(
        'documentReady',
        'smartslider-frontend'
    );

    protected $javaScriptProperties;

    /** @var  SliderWidget */
    protected $widgets;

    protected $shapeDividerAdded = false;

    protected $style = '';

    public function __construct($slider) {
        $this->slider = $slider;
        $this->jsDependency[] = 'nextend-gsap';
    

        $this->enqueueAssets();
    }

    /**
     * @param AbstractSliderTypeCss $css
     *
     * @return string
     */
    public function render($css) {

        $this->javaScriptProperties = $this->slider->features->generateJSProperties();

        $this->widgets = new SliderWidget($this->slider);

        ob_start();
        $this->renderType($css);

        return ob_get_clean();
    }

    /**
     * @param AbstractSliderTypeCss $css
     *
     * @return string
     */
    protected abstract function renderType($css);

    protected function getSliderClasses() {
        $alias      = $this->slider->getAlias();
        $fadeOnLoad = $this->slider->features->fadeOnLoad->getSliderClass();

        return $alias . ' ' . $fadeOnLoad;
    }

    protected function openSliderElement() {

        $attributes = array(
                'id'           => $this->slider->elementId,
                'data-creator' => 'Smart Slider 3',
                'class'        => 'n2-ss-slider n2-ow n2-has-hover n2notransition ' . $this->getSliderClasses(),

            ) + $this->getFontSizeAttributes();

        $alias = $this->slider->getAlias();
        if (!empty($alias)) {
            $attributes['data-alias'] = $alias;
        }

        return Html::openTag('div', $attributes);
    }

    protected function closeSliderElement() {

        return '</div>';
    }

    private function getFontSizeAttributes() {

        return array(
            'style'         => "font-size: 1rem;",
            'data-fontsize' => $this->slider->fontSize
        );
    }

    public function getDefaults() {
        return array();
    }

    /**
     * @param $params Data
     */
    public function limitParams($params) {

    }

    protected function encodeJavaScriptProperties() {

        $initCallback = implode($this->javaScriptProperties['initCallbacks']);
        unset($this->javaScriptProperties['initCallbacks']);

        $encoded = array();
        foreach ($this->javaScriptProperties as $k => $v) {
            $encoded[] = '"' . $k . '":' . json_encode($v);
        }
        $encoded[] = '"initCallbacks":function($){' . $initCallback . '}';

        return '{' . implode(',', $encoded) . '}';
    }


    protected function initParticleJS() {
        $particle = $this->slider->params->get('particle');
        if ($this->slider->isAdmin || empty($particle)) {
            return;
        }
        $particle = new Data($particle, true);
        $preset   = $particle->get('preset', '0');
        if ($preset != '0') {

            Js::addStaticGroup(ResourceTranslator::toPath('$ss3-pro-frontend$/dist/particle.min.js'), 'particles');

            $custom = $particle->get('custom', '');
            if ($preset == 'custom' && is_array($custom)) {
                $jsProp = $custom;
            } else {
                $jsProp = json_decode(Filesystem::readFile(ResourceTranslator::toPath('$ss3-pro-frontend$/js/particle/presets/' . $particle->get('preset') . '.json')), true);

                $color                                   = Color::colorToSVG($particle->get('color'));
                $jsProp['particles']["color"]["value"]   = '#' . $color[0];
                $jsProp['particles']["opacity"]["value"] = $color[1];

                $lineColor                                     = Color::colorToSVG($particle->get('line-color'));
                $jsProp['particles']["line_linked"]["color"]   = '#' . $lineColor[0];
                $jsProp['particles']["line_linked"]["opacity"] = $lineColor[1];

                $hover = $particle->get('hover', 0);
                if ($hover == '0') {
                    $jsProp['interactivity']["events"]["onhover"]['enable'] = 0;
                } else {
                    $jsProp['interactivity']["events"]["onhover"]['enable'] = 1;
                    $jsProp['interactivity']["events"]["onhover"]['mode']   = $hover;
                }

                $click = $particle->get('click', 0);
                if ($click == '0') {
                    $jsProp['interactivity']["events"]["onclick"]['enable'] = 0;
                } else {
                    $jsProp['interactivity']["events"]["onclick"]['enable'] = 1;
                    $jsProp['interactivity']["events"]["onclick"]['mode']   = $click;
                }

                $jsProp['particles']["number"]["value"] = max(10, min(200, $particle->get('number')));

                $jsProp['particles']["move"]["speed"] = max(1, min(60, $particle->get('speed')));
            }

            $jsProp['mobile'] = intval($particle->get('mobile', 0));

            $this->javaScriptProperties['particlejs'] = $jsProp;
        }
    
    }

    protected function renderShapeDividers() {
        $shapeDividers = $this->slider->params->get('shape-divider');
        if (!empty($shapeDividers)) {
            $shapeDividers = json_decode($shapeDividers, true);
            if ($shapeDividers) {
                $this->renderShapeDivider('top', $shapeDividers['top']);
                $this->renderShapeDivider('bottom', $shapeDividers['bottom']);
            }
        }
    
    }

    private function renderShapeDivider($side, $params) {
        $data = new Data($params);
        $type = $data->get('type', "0");
        if ($type != "0") {
            preg_match('/([a-z]+)\-(.*)/', $type, $matches);

            $type = $matches[2];
            switch ($matches[1]) {
                case 'bi':
                    $type = 'bicolor/' . $type;
                    break;
            }

            $file = ResourceTranslator::toPath('$ss3-pro-frontend$/shapedivider/' . $type . '.svg');
            if (Filesystem::existsFile($file)) {

                $animate = $data->get('animate') == '1';

                if ($animate) {
                    Js::addStaticGroup(ResourceTranslator::toPath('$ss3-pro-frontend$/dist/GsapMorphSVGPlugin.min.js'), 'GsapMorphSVGPlugin');
                }

                $outer = array(
                    'class'                       => 'n2-ss-shape-divider n2-ss-shape-divider-' . $side . ($animate ? ' n2-ss-divider-animate' : ''),
                    'style'                       => 'height:' . $data->get('desktopportraitheight') . 'px;',
                    'data-desktopportraitheight'  => $data->get('desktopportraitheight'),
                    'data-desktoplandscapeheight' => $data->get('desktoplandscapeheight'),
                    'data-tabletportraitheight'   => $data->get('tabletportraitheight'),
                    'data-tabletlandscapeheight'  => $data->get('tabletlandscapeheight'),
                    'data-mobileportraitheight'   => $data->get('mobileportraitheight'),
                    'data-mobilelandscapeheight'  => $data->get('mobilelandscapeheight'),
                    'data-scroll'                 => $data->get('scroll', 0),
                    'data-speed'                  => $data->get('speed', 100),
                    'data-side'                   => $side
                );

                $inner = array(
                    'class'                      => 'n2-ss-shape-divider-inner',
                    'style'                      => 'width:' . $data->get('desktopportraitwidth') . '%;margin-left:' . (($data->get('desktopportraitwidth') - 100) / -2) . '%;',
                    'data-desktopportraitwidth'  => $data->get('desktopportraitwidth'),
                    'data-desktoplandscapewidth' => $data->get('desktoplandscapewidth'),
                    'data-tabletportraitwidth'   => $data->get('tabletportraitwidth'),
                    'data-tabletlandscapewidth'  => $data->get('tabletlandscapewidth'),
                    'data-mobileportraitwidth'   => $data->get('mobileportraitwidth'),
                    'data-mobilelandscapewidth'  => $data->get('mobilelandscapewidth')
                );

                $svg = Filesystem::readFile($file);
                if (!$animate) {
                    $svg = preg_replace('/<g.*?class="n2-ss-divider-start".*?<\/g>/s', '', $svg);
                }

                if ($side == 'bottom') {
                    if ($data->get('flip') == '1') {
                        $svg = SVGFlip::mirror($svg, true, true);
                    } else {
                        $svg = SVGFlip::mirror($svg, false, true);
                    }
                } else {
                    if ($data->get('flip') == '1') {
                        $svg = SVGFlip::mirror($svg, true, false);
                    }
                }

                echo Html::tag('div', $outer, Html::tag('div', $inner, str_replace(array(
                    '#000000',
                    '#000010'
                ), array(
                    Color::colorToRGBA($data->get('color')),
                    Color::colorToRGBA($data->get('color2'))
                ), $svg)));

                if (!$this->shapeDividerAdded) {

                    $path = ResourceTranslator::toPath('$ss3-pro-frontend$/dist/shapedivider.min.js');
                    if (file_exists($path)) {
                        $this->javaScriptProperties['initCallbacks'][] = file_get_contents($path);
                    } else {
                    }

                    $this->shapeDividerAdded = true;
                }
            }
        }
    
    }

    /**
     * @return string
     */
    public function getScript() {
        return '';
    }

    public function getStyle() {
        return $this->style;
    }

    public function setJavaScriptProperty($key, $value) {
        $this->javaScriptProperties[$key] = $value;
    }

    public function enqueueAssets() {

        Js::addStaticGroup(ApplicationTypeFrontend::getAssetsPath() . '/dist/smartslider-frontend.min.js', 'smartslider-frontend');
    }
}

class SVGFlip {

    private static $viewBoxX;
    private static $viewBoxY;

    /**
     * @param string $svg
     * @param bool   $x
     * @param bool   $y
     *
     * @return string
     */
    public static function mirror($svg, $x, $y) {
        /* @var callable $callable */

        if ($x && $y) {
            $callable = array(
                self::class,
                'xy'
            );
        } else if ($x) {
            $callable = array(
                self::class,
                'x'
            );
        } else if ($y) {
            $callable = array(
                self::class,
                'y'
            );
        } else {
            return $svg;
        }

        preg_match('/(viewBox)=[\'"](.*?)[\'"]/i', $svg, $viewBoxResult);
        $viewBox        = explode(' ', end($viewBoxResult));
        self::$viewBoxX = $viewBox[2];
        self::$viewBoxY = $viewBox[3];

        $pattern = '/(points|d)=[\'"](.*?)[\'"]/i';

        return preg_replace_callback($pattern, $callable, $svg);
    }

    private static function x($paths) {
        $path = $paths[2];
        if ($paths[1] == 'points') {
            $points = explode(' ', $path);
            for ($i = 0; $i < count($points); $i = $i + 2) {
                $points[$i] = self::$viewBoxX - $points[$i];
            }

            return 'points="' . implode(' ', $points) . '"';
        } else if ($paths[1] == 'd') {
            $path    = substr($path, 0, -1);
            $values  = explode(' ', $path);
            $newPath = '';
            for ($i = 0; $i < count($values); $i++) {
                $pathCommand = substr($values[$i], 0, 1);
                $pathPart    = substr($values[$i], 1);
                $points      = explode(',', $pathPart);
                for ($j = 0; $j < count($points); $j = $j + 2) {
                    switch ($pathCommand) {
                        case 'l':
                        case 'm':
                        case 'h':
                        case 'c':
                        case 's':
                        case 'q':
                        case 't':
                            $points[$j] = -$points[$j];
                            break;
                        case 'L':
                        case 'M':
                        case 'H':
                        case 'C':
                        case 'S':
                        case 'Q':
                        case 'T':
                            $points[$j] = self::$viewBoxX - $points[$j];
                            break;
                    }
                }
                $newPath .= $pathCommand . implode(',', $points);
            }

            return 'd="' . $newPath . 'z"';
        }
    }

    private static function y($paths) {
        $path = $paths[2];
        if ($paths[1] == 'points') {
            $points = explode(' ', $path);
            for ($i = 1; $i < count($points); $i = $i + 2) {
                $points[$i] = self::$viewBoxY - $points[$i];
            }

            return 'points="' . implode(' ', $points) . '"';
        } else if ($paths[1] == 'd') {
            $path    = substr($path, 0, -1);
            $values  = explode(' ', $path);
            $newPath = '';
            for ($i = 0; $i < count($values); $i++) {
                $pathCommand = substr($values[$i], 0, 1);
                $pathPart    = substr($values[$i], 1);
                $points      = explode(',', $pathPart);
                for ($j = 0; $j < count($points); $j = $j + 2) {
                    switch ($pathCommand) {
                        case 'v':
                            $points[$j] = -$points[$j];
                            break;
                        case 'V':
                            $points[$j] = self::$viewBoxY - $points[$j];
                            break;
                        case 'l':
                        case 'm':
                        case 'c':
                        case 's':
                        case 'q':
                        case 't':
                            $points[$j + 1] = -$points[$j + 1];
                            break;
                        case 'L':
                        case 'M':
                        case 'C':
                        case 'S':
                        case 'Q':
                        case 'T':
                            $points[$j + 1] = self::$viewBoxY - $points[$j + 1];
                            break;
                    }
                }
                $newPath .= $pathCommand . implode(',', $points);
            }

            return 'd="' . $newPath . 'z"';
        }
    }

    private static function xy($paths) {
        $path = $paths[2];
        if ($paths[1] == 'points') {
            $points = explode(' ', $path);
            for ($i = 0; $i < count($points); $i = $i + 2) {
                $points[$i]     = self::$viewBoxX - $points[$i];
                $points[$i + 1] = self::$viewBoxY - $points[$i + 1];
            }

            return 'points="' . implode(' ', $points) . '"';
        } else if ($paths[1] == 'd') {
            $path    = substr($path, 0, -1);
            $values  = explode(' ', $path);
            $newPath = '';
            for ($i = 0; $i < count($values); $i++) {
                $pathCommand = substr($values[$i], 0, 1);
                $pathPart    = substr($values[$i], 1);
                $points      = explode(',', $pathPart);
                for ($j = 0; $j < count($points); $j = $j + 2) {
                    switch ($pathCommand) {
                        case 'h':
                        case 'v':
                            $points[$j] = -$points[$j];
                            break;
                        case 'H':
                            $points[$j] = self::$viewBoxX - $points[$j];
                            break;
                        case 'V':
                            $points[$j] = self::$viewBoxY - $points[$j];
                            break;
                        case 'l':
                        case 'm':
                        case 'c':
                        case 's':
                        case 'q':
                        case 't':
                            $points[$j]     = -$points[$j];
                            $points[$j + 1] = -$points[$j + 1];
                            break;
                        case 'L':
                        case 'M':
                        case 'C':
                        case 'S':
                        case 'Q':
                        case 'T':
                            $points[$j]     = self::$viewBoxX - $points[$j];
                            $points[$j + 1] = self::$viewBoxY - $points[$j + 1];
                            break;
                    }
                }
                $newPath .= $pathCommand . implode(',', $points);
            }

            return 'd="' . $newPath . 'z"';
        }
    }
}