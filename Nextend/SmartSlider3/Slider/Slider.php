<?php


namespace Nextend\SmartSlider3\Slider;


use Exception;
use Nextend\Framework\Asset\AssetManager;
use Nextend\Framework\Asset\Css\Css;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Pattern\MVCHelperTrait;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Renderable\AbstractRenderable;
use Nextend\SmartSlider3\Slider\Base\PlatformSliderTrait;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeCss;
use Nextend\SmartSlider3\Slider\SliderType\AbstractSliderTypeFrontend;
use Nextend\SmartSlider3\Slider\SliderType\SliderTypeFactory;


class Slider extends AbstractRenderable {

    use PlatformSliderTrait, MVCHelperTrait;

    const LOAD_STATE_NONE = 0;
    const LOAD_STATE_SLIDER = 1;
    const LOAD_STATE_SLIDES = 2;
    const LOAD_STATE_ALL = 3;

    protected $loadState;

    protected $isAdminArea = false;

    public $manifestData = array(
        'generator' => array()
    );

    protected $isGroup = false;

    public $hasError = false;

    public $sliderId = 0;

    public $cacheId = '';

    /** @var  Data */
    public $data;

    public $disableResponsive = false;

    protected $parameters = array(
        'disableResponsive' => false,
        'sliderData'        => array(),
        'slidesData'        => array(),
        'generatorData'     => array()
    );

    public $fontSize = 16;

    /**
     * @var Slides
     */
    protected $slidesBuilder;

    protected $cache = false;

    public static $_identifier = 'n2-ss';

    /** @var Slide[] */
    public $staticSlides = array();

    /** @var  AbstractSliderTypeFrontend */
    protected $sliderType;

    /**
     * @var AbstractSliderTypeCss
     */
    public $assets;

    public $staticHtml = '';

    private $sliderRow;

    public $exposeSlideData = array(
        'title'         => true,
        'description'   => false,
        'thumbnail'     => false,
        'thumbnailType' => false,
        'lightboxImage' => false
    );

    /**
     * @var Data
     */
    public $params;

    /**
     * @var Slide
     */
    protected $activeSlide;

    /**
     * Slider constructor.
     *
     * @param MVCHelperTrait $MVCHelper
     * @param                $sliderId
     * @param                $parameters
     * @param                $isAdminArea
     */
    public function __construct($MVCHelper, $sliderId, $parameters, $isAdminArea = false) {
        $this->loadState = self::LOAD_STATE_NONE;

        $this->isAdminArea = $isAdminArea;

        $this->setMVCHelper($MVCHelper);

        $this->initPlatformSlider();

        $this->sliderId = $sliderId;

        $this->setElementId();

        $this->cacheId = static::getCacheId($this->sliderId);

        $this->parameters = array_merge($this->parameters, $parameters);

        $this->disableResponsive = $this->parameters['disableResponsive'];
    }


    public function setElementId() {
        $this->elementId = self::$_identifier . '-' . $this->sliderId;
    }

    public static function getCacheId($sliderId) {
        return self::$_identifier . '-' . $sliderId;
    }

    public function getAlias() {
        return $this->data->get('alias', '');
    }

    /**
     * @throws Exception
     */
    public function initSlider() {
        if ($this->loadState < self::LOAD_STATE_SLIDER) {

            $slidersModel = new ModelSliders($this->MVCHelper);
            $sliderRow    = $slidersModel->get($this->sliderId);

            if (empty($sliderRow)) {
                $this->hasError = true;
                throw new Exception('Slider does not exists!');
            } else {

                if (!$this->isAdminArea && $sliderRow['status'] != 'published') {
                    $this->hasError = true;
                    throw new Exception('Slider is not published!');
                }

                if (!empty($this->parameters['sliderData'])) {
                    $sliderData         = $this->parameters['sliderData'];
                    $sliderRow['title'] = $sliderData['title'];
                    unset($sliderData['title']);
                    $sliderRow['type'] = $sliderData['type'];
                    unset($sliderData['type']);

                    $this->data   = new Data($sliderRow);
                    $this->params = new SliderParams($sliderRow['type'], $sliderData);
                } else {
                    $this->data   = new Data($sliderRow);
                    $this->params = new SliderParams($sliderRow['type'], $sliderRow['params'], true);
                }

                switch ($sliderRow['type']) {
                    case 'group':
                        $this->isGroup = true;
                        break;
                }
            }

            $this->loadState = self::LOAD_STATE_SLIDER;
        }
    }

    /**
     * @throws Exception
     */
    public function initSlides() {
        if ($this->loadState < self::LOAD_STATE_SLIDES) {

            $this->initSlider();

            if (!$this->isGroup) {
                $this->slidesBuilder = new Slides($this);

                $this->slidesBuilder->initSlides($this->parameters['slidesData'], $this->parameters['generatorData']);
            }

            $this->loadState = self::LOAD_STATE_SLIDES;
        }
    }

    /**
     * @throws Exception
     */
    public function initAll() {
        if ($this->loadState < self::LOAD_STATE_ALL) {

            $this->initSlides();


            $this->loadState = self::LOAD_STATE_ALL;
        }
    }

    private function loadSlider() {

        $this->sliderType = SliderTypeFactory::createFrontend($this->data->get('type', 'simple'), $this);
        $defaults         = $this->sliderType->getDefaults();

        $parallaxOverlap = $this->params->get('animation-parallax-overlap', false);

        if ($parallaxOverlap === false) {
            $animationParallax = $this->params->get('animation-parallax', false);
            if ($animationParallax !== false) {
                $parallaxOverlap = 100 - floatval($animationParallax) * 100;
            } else {
                $parallaxOverlap = 0;
            }
            $this->params->set('animation-parallax-overlap', $parallaxOverlap);
            $this->params->un_set('animation-parallax');
        }

        $this->params->fillDefault($defaults);
        $this->sliderType->limitParams($this->params);

        if (!$this->isGroup) {
            $this->features = new FeatureManager($this);
        }

        return true;
    }

    public function getNextCacheRefresh() {
        if ($this->isGroup) {
            return $this->sliderType->getNextCacheRefresh();
        }

        return $this->slidesBuilder->getNextCacheRefresh();
    }

    /**
     * @return string
     * @throws Exception
     */
    public function render() {
        if ($this->loadState < self::LOAD_STATE_ALL) {
            throw new Exception('Load state not reached all!');
        }

        if (!$this->loadSlider()) {
            return false;
        }

        if (!$this->isGroup) {
            if (!$this->hasSlides()) {
                $this->slidesBuilder->addDummySlides();
            }

            if (!$this->getActiveSlide()) {
                $slides = $this->getSlides();
                $this->setActiveSlide($slides[0]);
            }

            $this->getActiveSlide()
                 ->setFirst();
        }

        $this->assets = SliderTypeFactory::createCss($this->data->get('type', 'simple'), $this);

        if (!$this->isGroup) {

            $this->slidesBuilder->prepareRender();

            $this->renderStaticSlide();
        }
        $slider = $this->sliderType->render($this->assets);

        $slider = str_replace('n2-ss-0', $this->elementId, $slider);
        if (!$this->isAdmin) {
            $rocketAttributes = '';

            $loadingType = $this->params->get('loading-type');
            if ($loadingType == 'afterOnLoad') {
                $rocketAttributes .= 'data-loading-type="' . $loadingType . '"';
            } else if ($loadingType == 'afterDelay') {

                $delay = max(0, intval($this->params->get('delay'), 0));
                if ($delay > 0) {
                    $rocketAttributes .= 'data-loading-type="' . $loadingType . '"';
                    $rocketAttributes .= 'data-loading-delay="' . $delay . '"';
                }
            }

            if (!empty($rocketAttributes)) {
                $slider = '<template id="' . $this->elementId . '" ' . $rocketAttributes . '>' . $slider . '</template>';
            }
        }
        if (!$this->isGroup) {
            $slider = $this->features->translateUrl->renderSlider($slider);

            $slider = $this->features->loadSpinner->renderSlider($this, $slider);
            $slider = $this->features->align->renderSlider($slider, $this->assets->sizes['width']);
            $slider = $this->features->margin->renderSlider($slider);


            $style = $this->sliderType->getStyle();
            if ($this->isAdmin) {
                $slider = '<style type="text/css">' . $style . '</style>' . $slider;
            } else {
                $cssMode = \Nextend\Framework\Settings::get('css-mode', 'normal');
                switch ($cssMode) {
                    case 'inline':
                        Css::addInline($style);
                        break;
                    case 'async':
                        $this->sliderType->setJavaScriptProperty('css', $style);
                        break;
                    default:
                        $slider = '<style>' . $style . '</style>' . $slider;
                        break;
                }
            }


            $slider .= $this->features->fadeOnLoad->renderPlaceholder($this->assets->sizes);

            $jsInlineMode = \Nextend\Framework\Settings::get('javascript-inline', 'head');
            if (class_exists('ElementorPro\Plugin', false)) {
                $jsInlineMode = 'body';
            }
        
            switch ($jsInlineMode) {
                case 'body':
                    $slider .= Html::script($this->sliderType->getScript());
                    break;
                case 'head':
                default:
                    Js::addInline($this->sliderType->getScript());
                    break;
            }
        }

        $html = '';

        $classes = array(
            'n2-section-smartslider',
            'fitvidsignore',
            $this->params->get('classes', '')
        );

        if (intval($this->params->get('clear-both', 1))) {
            $classes[] = 'n2_clear';
        }

        $html .= Html::tag("div", array(
            'class'      => implode(' ', $classes),
            'role'       => 'region',
            'aria-label' => $this->params->get('aria-label', 'Slider')
        ), $slider);

        if (!$this->params->get('optimize-jetpack-photon', 0)) {
            AssetManager::$image->add($this->images);
        }

        return $html;
    }

    public function addStaticSlide($slide) {
        $this->staticSlides[] = $slide;
    }

    public function renderStaticSlide() {
        $this->staticHtml = '';
        if (count($this->staticSlides)) {
            for ($i = 0; $i < count($this->staticSlides); $i++) {
                $this->staticHtml .= $this->staticSlides[$i]->getAsStatic();
            }
        }
    }

    public static function removeShortcode($content) {
        $content = preg_replace('/smartslider3\[([0-9]+)\]/', '', $content);
        $content = preg_replace('/\[smartslider3 slider="([0-9]+)"\]/', '', $content);
        $content = preg_replace('/\[smartslider3 slider=([0-9]+)\]/', '', $content);

        return $content;
    }

    /**
     * @return Slide
     */
    public function getActiveSlide() {
        return $this->activeSlide;
    }

    /**
     * @param Slide $activeSlide
     */
    public function setActiveSlide($activeSlide) {
        $this->activeSlide = $activeSlide;
    }

    /**
     * @return Slide[]
     */
    public function getSlides() {
        return $this->slidesBuilder->getSlides();
    }

    /**
     * @return bool
     */
    public function hasSlides() {
        if ($this->isGroup) {
            return true;
        }

        return $this->slidesBuilder->hasSlides();
    }

    /**
     * @return int
     */
    public function getSlidesCount() {
        if ($this->isGroup) {
            return 0;
        }

        return $this->slidesBuilder->getSlidesCount();
    }

    public function isGroup() {
        $this->initSlider();

        return $this->isGroup;
    }
}