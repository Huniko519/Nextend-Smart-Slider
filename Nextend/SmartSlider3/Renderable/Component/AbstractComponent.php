<?php


namespace Nextend\SmartSlider3\Renderable\Component;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\BackupSlider\ExportSlider;
use Nextend\SmartSlider3\BackupSlider\ImportSlider;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\ComponentContainer;
use Nextend\SmartSlider3\Renderable\Placement\AbstractPlacement;
use Nextend\SmartSlider3\Renderable\Placement\PlacementAbsolute;
use Nextend\SmartSlider3\Renderable\Placement\PlacementDefault;
use Nextend\SmartSlider3\Renderable\Placement\PlacementNormal;
use Nextend\SmartSlider3\Slider\Slide;

abstract class AbstractComponent {

    public static $isAdmin = false;

    /**
     * @var Slide
     */
    protected $owner;

    protected $type = '';

    protected $name = '';
    /**
     * @var AbstractComponent|bool
     */
    protected $group;

    /**
     * @var AbstractPlacement
     */
    protected $placement;

    /**
     * @var ComponentContainer
     */
    protected $container = false;

    protected $fontSizeModifier = 100;

    protected $baseSize = 16;

    protected $attributes = array(
        'class' => 'n2-ss-layer n2-ow',
        'style' => ''
    );

    public $data;

    protected $localStyle = array();

    protected $hasBackground = false;

    /**
     * AbstractBuilderComponent constructor.
     *
     * @param int                                 $index
     * @param AbstractRenderableOwner             $owner
     * @param AbstractComponent|bool              $group
     * @param                                     $data
     */
    public function __construct($index, $owner, $group, $data) {
        $this->owner = $owner;
        $this->group = $group;

        $this->data = new Data($data);

        $this->fontSizeModifier = $this->data->get('desktopportraitfontsize', 100);
        if (!is_numeric($this->fontSizeModifier)) {
            $this->fontSizeModifier = 100;
        }

        $this->baseSize = $group ? $group->getBaseSize() : 16;

        switch ($this->getPlacement()) {
            case 'normal':
                $this->placement = new PlacementNormal($this, $index);
                break;
            case 'default':
                $this->placement = new PlacementDefault($this, $index);
                break;
            case 'absolute':
            default:
                $this->placement = new PlacementAbsolute($this, $index);
                break;
        }
    }

    public function getPlacement() {

        if ($this->data->has('pm')) {
            return $this->data->get('pm');
        }

        if ($this->group->getType() == 'slide') {
            return 'absolute';
        }

        return 'normal';
    }

    /**
     * @return AbstractRenderableOwner
     */
    public function getOwner() {
        return $this->owner;
    }

    /**
     * @return int
     */
    public function getBaseSize() {
        return $this->baseSize * $this->fontSizeModifier / 100;
    }

    public function isRenderAllowed() {
        $generatorVisible = $this->data->get('generatorvisible', '');
        if ($this->owner->isComponentVisible($generatorVisible) && !self::$isAdmin) {
            $filled = $this->owner->fill($generatorVisible);
            if (empty($filled)) {
                return false;
            }
        }

        return true;
    }

    public abstract function render($isAdmin);

    protected function renderContainer($isAdmin) {

        if ($this->container) {
            return $this->container->render($isAdmin);
        }

        return '';
    }

    protected function admin() {

        $this->createProperty('id', '');
        $this->createProperty('uniqueclass', '');
        $this->createProperty('zindex', 2);
        $this->createProperty('class', '');
        $this->createProperty('name', $this->name);
        $this->createProperty('namesynced', 1);
        $this->createProperty('status');
        $this->createProperty('generatorvisible', '');

        $this->placement->adminAttributes($this->attributes);
    }

    public function pxToEm($value) {
        $unit     = 'px';
        $baseSize = $this->getBaseSize();
        if ($baseSize > 0) {
            $unit  = 'em';
            $value = intval($value) / $baseSize;
        }

        return $value . $unit;
    }

    public function spacingToEm($value) {
        $values   = explode('|*|', $value);
        $unit     = $values[4];
        $baseSize = $this->getBaseSize();
        if ($unit == 'px+' && $baseSize > 0) {
            $unit = 'em';
            for ($i = 0; $i < 4; $i++) {
                $values[$i] = intval($values[$i]) / $baseSize;
            }
        }
        $values[4] = '';

        return implode($unit . ' ', $values);
    }

    protected function prepareHTML() {
        $this->attributes['data-sstype'] = $this->type;

        $id = $this->data->get('id', '');
        if (!empty($id)) {
            $this->attributes['id'] = $id;
        }

        $class = $this->data->get('class', '');
        if (!empty($class)) {
            $this->attributes['class'] .= ' ' . $this->getOwner()
                                                     ->fill($class);
        }

        $uniqueClass = $this->data->get('uniqueclass', '');
        if (!empty($uniqueClass)) {
            $this->addUniqueClass($uniqueClass . $this->owner->unique);
        }

        $zIndex = intval($this->data->get('zindex', 2));
        if ($zIndex != 2) {
            $this->attributes['style'] .= 'z-index:' . $zIndex . ';';
        }

    }

    protected function addUniqueClass($class) {
        $this->attributes['class'] .= ' ' . $class;
    }

    protected function renderPlugins($html) {
        $this->pluginRotation();
        $html = $this->pluginCrop($html);
        $this->pluginAnimations();
    
        $this->pluginShowOn();
        $this->pluginFontSize();
        $this->pluginParallax();
        $this->attributes['data-plugin'] = 'rendered';

        return $html;
    }

    private function pluginRotation() {

        $this->createProperty('rotation', 0);
    }

    private function pluginCrop($html) {

        $cropStyle = $this->data->get('crop', 'visible');

        if (self::$isAdmin) {
            if ($cropStyle == 'auto') {
                $cropStyle = 'hidden';
            }
        } else {
            if ($cropStyle == 'auto') {
                $this->attributes['class'] .= ' n2_container_scrollable';
            }
        }

        if ($cropStyle == 'mask') {
            $cropStyle = 'hidden';
            $html      = Html::tag('div', array('class' => 'n2-ss-layer-mask'), $html);

            $this->attributes['data-animatableselector'] = '.n2-ss-layer-mask:first';
        } else if (!self::$isAdmin && $this->data->get('parallax', 0) > 0) {
            $html = Html::tag('div', array(
                'class' => 'n2-ss-layer-parallax'
            ), $html);

            $this->attributes['data-animatableselector'] = '.n2-ss-layer-parallax:first';
        }

        $this->attributes['style'] .= 'overflow:' . $cropStyle . ';';

        if (self::$isAdmin) {
            $crop = $this->data->get('crop', 'visible');
            if (empty($crop)) $crop = 'visible';
            $this->attributes['data-crop'] = $crop;
        }

        return $html;
    }

    /**
     * Transform V1 animations to V2
     *
     * @param $data
     *
     * @return array
     */
    private function pluginAnimationsConvertV1ToV2($data) {
        if (empty($data)) {
            return array();
        }

        if (isset($data['in'])) {
            if (!isset($data['basic'])) {
                $data['basic'] = array(
                    'in' => array()
                );
            } else if (!isset($data['basic']['in'])) {
                $data['basic']['in'] = array();
            }
            $this->pluginAnimationsConvertV1ToV2RemoveName($data['in']);
            if (isset($data['in'][0]['delay']) && isset($data['repeatable']) && $data['repeatable'] == 1) {
                if ($data['in'][0]['delay'] > 0) {
                    $data['startDelay'] = $data['in'][0]['delay'];
                }
                unset($data['in'][0]['delay']);
            }
            $data['basic']['in']['keyFrames'] = $data['in'];
            unset($data['in']);
        }

        if (isset($data['specialZeroIn'])) {
            if (isset($data['basic']['in'])) {
                $data['basic']['in']['specialZero'] = $data['specialZeroIn'];
            }
            unset($data['specialZeroIn']);
        }

        if (isset($data['transformOriginIn'])) {
            if (isset($data['basic']['in'])) {
                $data['basic']['in']['transformOrigin'] = $data['transformOriginIn'];
            }
            unset($data['transformOriginIn']);
        }

        if (isset($data['loop'])) {
            if (!isset($data['basic'])) {
                $data['basic'] = array(
                    'loop' => array()
                );
            } else if (!isset($data['basic']['loop'])) {
                $data['basic']['loop'] = array();
            }
            $this->pluginAnimationsConvertV1ToV2RemoveName($data['loop']);
            $data['basic']['loop']['keyFrames'] = $data['loop'];
            unset($data['loop']);
        }

        if (isset($data['repeatCount'])) {
            if (isset($data['basic']['loop'])) {
                $data['basic']['loop']['repeatCount'] = $data['repeatCount'];
            }
            unset($data['repeatCount']);
        }

        if (isset($data['repeatStartDelay'])) {
            if (isset($data['basic']['loop'])) {
                $data['basic']['loop']['repeatStartDelay'] = $data['repeatStartDelay'];
            }
            unset($data['repeatStartDelay']);
        }

        if (isset($data['transformOriginLoop'])) {
            if (isset($data['basic']['loop'])) {
                $data['basic']['loop']['transformOrigin'] = $data['transformOriginLoop'];
            }
            unset($data['transformOriginLoop']);
        }

        if (isset($data['out'])) {
            if (!isset($data['basic'])) {
                $data['basic'] = array(
                    'out' => array()
                );
            } else if (!isset($data['basic']['out'])) {
                $data['basic']['out'] = array();
            }
            $this->pluginAnimationsConvertV1ToV2RemoveName($data['out']);
            $data['basic']['out']['keyFrames'] = $data['out'];
            unset($data['out']);
        }

        if (isset($data['transformOriginOut'])) {
            if (isset($data['basic']['out'])) {
                $data['basic']['out']['transformOrigin'] = $data['transformOriginOut'];
            }
            unset($data['transformOriginOut']);
        }

        if (!isset($data['instantOut']) || $data['instantOut'] == '1') {
            if (empty($data['outPlayEvent']) && $this->owner->getSlider()->params->get('layer-animation-play-mode') === 'forced') {
                $data['outPlayEvent'] = 'InstantOut';
            }
        }

        if (isset($data['instantOut'])) {
            unset($data['instantOut']);
        }

        return $data;
    }

    private function pluginAnimationsConvertV1ToV2RemoveName(&$keyFrames) {
        for ($i = 0; $i < count($keyFrames); $i++) {
            if (isset($keyFrames[$i]['name'])) {
                unset($keyFrames[$i]['name']);
            }
        }

    }


    private function pluginAnimations() {
        $animations = $this->data->get('animv2', -1);
        if ($animations === -1) {
            $animationsV1 = $this->data->get('animations', -1);
            if ($animationsV1 !== -1) {
                $animations = $this->pluginAnimationsConvertV1ToV2($animationsV1);
            }
        }

        if ($animations === -1) {
            $animations = '';
        }

        if (!empty($animations)) {

            if (isset($animations['basic'])) {
                /**
                 * Empty keyFrame gets encoded as array instead of object and arrays might have extra property
                 * which can mess up animations.
                 */
                if (isset($animations['basic']['in'])) {
                    if (empty($animations['basic']['in'])) {
                        unset($animations['basic']['in']);
                    } else {
                        self::fixAnimationArray($animations['basic']['in'], 'keyFrames');
                    }
                }
                if (isset($animations['basic']['loop'])) {
                    if (empty($animations['basic']['loop'])) {
                        unset($animations['basic']['loop']);
                    } else {
                        self::fixAnimationArray($animations['basic']['loop'], 'keyFrames');
                    }
                }
                if (isset($animations['basic']['out'])) {
                    if (empty($animations['basic']['out'])) {
                        unset($animations['basic']['out']);
                    } else {
                        self::fixAnimationArray($animations['basic']['out'], 'keyFrames');
                    }
                }
            }

            if (isset($animations['reveal'])) {
                if (isset($animations['reveal']['in'])) {
                    $animations['reveal']['in'] = (object)$animations['reveal']['in'];
                }
                if (isset($animations['reveal']['out'])) {
                    $animations['reveal']['out'] = (object)$animations['reveal']['out'];
                }
            }

            $this->attributes['data-animv2'] = json_encode($animations);
        }

        $this->pluginAnimationGetEventAttributes();
    
    }

    private static function fixAnimationArray(&$array, $key) {
        if (isset($array[$key]) && is_array($array[$key])) {
            for ($i = 0; $i < count($array[$key]); $i++) {
                $array[$key][$i] = (object)$array[$key][$i];
            }
        }
    }


    private function pluginAnimationGetEventAttributes() {

        if (!self::$isAdmin) {
            $elementID = $this->owner->getElementID();

            $click = $this->data->get('click');
            if (!empty($click)) {
                $this->attributes['data-click'] = $this->pluginAnimationParseEventCode($click, $elementID);
            }
            $mouseenter = $this->data->get('mouseenter');
            if (!empty($mouseenter)) {
                $this->attributes['data-mouseenter'] = $this->pluginAnimationParseEventCode($mouseenter, $elementID);
            }
            $mouseleave = $this->data->get('mouseleave');
            if (!empty($mouseleave)) {
                $this->attributes['data-mouseleave'] = $this->pluginAnimationParseEventCode($mouseleave, $elementID);
            }
            $play = $this->data->get('play');
            if (!empty($play)) {
                $this->attributes['data-play'] = $this->pluginAnimationParseEventCode($play, $elementID);
            }
            $pause = $this->data->get('pause');
            if (!empty($pause)) {
                $this->attributes['data-pause'] = $this->pluginAnimationParseEventCode($pause, $elementID);
            }
            $stop = $this->data->get('stop');
            if (!empty($stop)) {
                $this->attributes['data-stop'] = $this->pluginAnimationParseEventCode($stop, $elementID);
            }
        } else {

            $click = $this->data->get('click');
            if (!empty($click)) {
                $this->attributes['data-click'] = $click;
            }
            $mouseenter = $this->data->get('mouseenter');
            if (!empty($mouseenter)) {
                $this->attributes['data-mouseenter'] = $mouseenter;
            }
            $mouseleave = $this->data->get('mouseleave');
            if (!empty($mouseleave)) {
                $this->attributes['data-mouseleave'] = $mouseleave;
            }
            $play = $this->data->get('play');
            if (!empty($play)) {
                $this->attributes['data-play'] = $play;
            }
            $pause = $this->data->get('pause');
            if (!empty($pause)) {
                $this->attributes['data-pause'] = $pause;
            }
            $stop = $this->data->get('stop');
            if (!empty($stop)) {
                $this->attributes['data-stop'] = $stop;
            }
        }
    }

    private function pluginAnimationParseEventCode($code, $elementId) {
        if (preg_match('/^[a-zA-Z0-9_\-,]+$/', $code)) {
            if (is_numeric($code)) {
                $code = "window['" . $elementId . "'].changeTo(" . ($code - 1) . ");";
            } else if ($code == 'next') {
                $code = "window['" . $elementId . "'].next();";
            } else if ($code == 'previous') {
                $code = "window['" . $elementId . "'].previous();";
            } else {
                $code = "n2ss.trigger(e.currentTarget, '" . $code . "');";
            }
        }

        return $code;
    }


    private function pluginShowOn() {
        $this->createDeviceProperty('', 1);
    }

    protected function pluginFontSize() {
        $this->attributes['data-adaptivefont'] = $this->data->get('adaptivefont', 0);

        $this->createDeviceProperty('fontsize', 100);
    }

    public function pluginParallax() {

        $parallax = intval($this->data->get('parallax', 0));
        if (self::$isAdmin || $parallax >= 1) {
            $this->attributes['data-parallax'] = $parallax;
        }
    }

    public function createProperty($name, $default = null) {
        $this->attributes['data-' . $name] = $this->data->get($name, $default);
    }

    public function createColorProperty($name, $allowVariable, $default = null) {
        $value = $this->data->get($name, $default);

        if (!$allowVariable || ($value !== NULL && $value[0] != '{')) {
            $l = strlen($value);
            if (($l != 6 && $l != 8) || !preg_match('/^[0-9A-Fa-f]+$/', $value)) {
                $value = $default;
            }
        }
        $this->attributes['data-' . $name] = $value;
    }

    public function createDeviceProperty($name, $default = null) {
        $device = 'desktopportrait';

        $this->attributes['data-' . $device . $name] = $this->data->get($device . $name, $default);

        $devices = array(
            'desktoplandscape',
            'tabletportrait',
            'tabletlandscape',
            'mobileportrait',
            'mobilelandscape'
        );
        foreach ($devices AS $device) {
            $this->attributes['data-' . $device . $name] = $this->data->get($device . $name, null);
        }
    }

    protected function renderBackground() {

        $gradientBackgroundProps = '';
        $background              = '';
        $image                   = $this->owner->fill($this->data->get('bgimage', ''));
        if ($image != '') {
            $x          = intval($this->data->get('bgimagex', 50));
            $y          = intval($this->data->get('bgimagey', 50));
            $background .= 'URL("' . ResourceTranslator::toUrl($image) . '") ' . $x . '% ' . $y . '% / cover no-repeat';

            $gradientBackgroundProps = ' ' . $x . '% ' . $y . '% / cover no-repeat';
        }

        $color    = $this->owner->fill($this->data->get('bgcolor', '00000000'));
        $gradient = $this->data->get('bgcolorgradient', 'off');
        $colorEnd = $this->owner->fill($this->data->get('bgcolorgradientend', '00000000'));
        $this->addLocalStyle('normal', 'background', $this->getBackgroundCSS($color, $gradient, $colorEnd, $background, $gradientBackgroundProps));


        $colorHover       = $this->data->get('bgcolor-hover');
        $gradientHover    = $this->data->get('bgcolorgradient-hover');
        $colorEndHover    = $this->data->get('bgcolorgradientend-hover');
        $isHoverDifferent = false;
        if (!empty($colorHover) && $colorHover != $color) {
            $isHoverDifferent = true;
        }
        if (!empty($gradientHover) && $gradientHover != $gradient) {
            $isHoverDifferent = true;
        }
        if (!empty($colorEndHover) && $colorEndHover != $colorEnd) {
            $isHoverDifferent = true;
        }
        if ($isHoverDifferent) {
            if (empty($colorHover)) $colorHover = $color;
            if (empty($gradientHover)) $gradientHover = $gradient;
            if (empty($colorEndHover)) $colorEndHover = $colorEnd;

            $this->addLocalStyle('hover', 'background', $this->getBackgroundCSS($colorHover, $gradientHover, $colorEndHover, $background, $gradientBackgroundProps));
        }
    }

    protected function getBackgroundCSS($color, $gradient, $colorend, $background, $gradientBackgroundProps) {
        if (Color::hex2alpha($color) != 0 || ($gradient != 'off' && Color::hex2alpha($colorend) != 0)) {
            $this->hasBackground = true;
            $after               = '';
            if ($background != '') {
                $after .= $gradientBackgroundProps . ',' . $background;
            }
            switch ($gradient) {
                case 'horizontal':
                    return 'background:linear-gradient(to right, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorend) . ' 100%)' . $after . ';';
                    break;
                case 'vertical':
                    return 'background:linear-gradient(to bottom, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorend) . ' 100%)' . $after . ';';
                    break;
                case 'diagonal1':
                    return 'background:linear-gradient(45deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorend) . ' 100%)' . $after . ';';
                    break;
                case 'diagonal2':
                    return 'background:linear-gradient(135deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorend) . ' 100%)' . $after . ';';
                    break;
                case 'off':
                default:
                    if ($background != '') {
                        return "background:linear-gradient(" . Color::colorToRGBA($color) . ", " . Color::colorToRGBA($color) . ")" . $after . ';';
                    } else {
                        return "background:" . Color::colorToRGBA($color) . ';';
                    }

                    break;
            }
        } else if (($background != '')) {
            $this->hasBackground = true;

            return "background:" . $background . ';';
        }

        return '';
    }

    /**
     * @param AbstractRenderableOwner $slide
     * @param array                   $layer
     */
    public static function getFilled($slide, &$layer) {
        if (!empty($layer['uniqueclass'])) {
            $layer['uniqueclass'] .= $slide->unique;
        }
        if (!empty($layer['class'])) {
            $layer['class'] = $slide->fill($layer['class']);
        }
    }

    /**
     * @param ExportSlider $export
     * @param array        $layer
     */
    public static function prepareExport($export, $layer) {

    }

    /**
     * @param ImportSlider $import
     * @param array        $layer
     */
    public static function prepareImport($import, &$layer) {

    }

    /**
     * @param array $layer
     */
    public static function prepareSample(&$layer) {

    }

    public function getAttribute($key) {
        if (isset($this->attributes[$key])) {
            return $this->attributes[$key];
        }

        return null;
    }

    public function setAttribute($key, $value) {
        $this->attributes[$key] = $value;
    }

    protected function addLocalStyle($group, $name, $style) {
        if (!empty($style)) {
            for ($i = 0; $i < count($this->localStyle); $i++) {
                if ($this->localStyle[$i]['group'] == $group) {
                    $this->localStyle[$i]['css'][$name] = $style;
                    break;
                }
            }
        }
    }

    protected function serveLocalStyle() {
        $css = '';
        for ($i = 0; $i < count($this->localStyle); $i++) {
            $style = '';
            if (count($this->localStyle[$i]['css']) == 1 && isset($this->localStyle[$i]['css']['transition'])) {
                unset($this->localStyle[$i]['css']['transition']);
            }
            foreach ($this->localStyle[$i]['css'] as $_css) {
                $style .= $_css;
            }
            if (!empty($style)) {
                $css .= '@rule' . $this->localStyle[$i]['selector'] . '{' . $style . '}';
            }
        }
        if (!empty($css)) {

            $uniqueClass = $this->data->get('uniqueclass', '');
            if (empty($uniqueClass)) {
                $uniqueClass = self::generateUniqueIdentifier('n-uc-');
                $this->data->set('uniqueclass', $uniqueClass);
            }
            $uniqueClass .= $this->owner->unique;

            $this->getOwner()
                 ->addCSS(str_replace('@rule', 'div#' . $this->owner->getElementID() . ' .' . $uniqueClass, $css));
        }
    }

    protected static function generateUniqueIdentifier($prefix = 'n', $length = 12) {
        $characters       = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString     = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
        }

        return $prefix . $randomString;
    }


    public static function translateUniqueIdentifier($layers, $isAction = true) {
        $idTranslation = array();

        self::translateUniqueIdentifierID($idTranslation, $layers);

        self::translateUniqueIdentifierParentID($idTranslation, $layers);

        if ($isAction) {
            self::translateUniqueIdentifierClass($layers);
        }

        return $layers;
    }

    private static function translateUniqueIdentifierID(&$idTranslation, &$layers) {
        if (is_array($layers)) {
            for ($i = 0; $i < count($layers); $i++) {
                if (!empty($layers[$i]['id'])) {
                    $newId                            = self::generateUniqueIdentifier();
                    $idTranslation[$layers[$i]['id']] = $newId;
                    $layers[$i]['id']                 = $newId;
                }
                if (isset($layers[$i]['type'])) {
                    switch ($layers[$i]['type']) {
                        case 'row':
                            self::translateUniqueIdentifierID($idTranslation, $layers[$i]['cols']);
                            break;
                        case 'col':
                        case 'content':
                            self::translateUniqueIdentifierID($idTranslation, $layers[$i]['layers']);
                            break;
                    }
                }
            }
        }
    }

    private static function translateUniqueIdentifierParentID(&$idTranslation, &$layers) {
        if (is_array($layers)) {
            for ($i = 0; $i < count($layers); $i++) {
                if (!empty($layers[$i]['parentid'])) {
                    if (isset($idTranslation[$layers[$i]['parentid']])) {
                        $layers[$i]['parentid'] = $idTranslation[$layers[$i]['parentid']];
                    } else {
                        $layers[$i]['parentid'] = '';
                    }
                }
                if (isset($layers[$i]['type'])) {
                    switch ($layers[$i]['type']) {
                        case 'row':
                            self::translateUniqueIdentifierParentID($idTranslation, $layers[$i]['cols']);
                            break;
                        case 'col':
                        case 'content':
                            self::translateUniqueIdentifierParentID($idTranslation, $layers[$i]['layers']);
                            break;
                    }
                }
            }
        }
    }

    private static function translateUniqueIdentifierClass(&$layers) {
        if (is_array($layers)) {
            for ($i = 0; $i < count($layers); $i++) {
                if (!empty($layers[$i]['uniqueclass'])) {
                    $layers[$i]['uniqueclass'] = self::generateUniqueIdentifier('n-uc-');
                }
                if (isset($layers[$i]['type'])) {
                    switch ($layers[$i]['type']) {
                        case 'row':
                            self::translateUniqueIdentifierClass($layers[$i]['cols']);
                            break;
                        case 'col':
                        case 'content':
                            self::translateUniqueIdentifierClass($layers[$i]['layers']);
                            break;
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }
}