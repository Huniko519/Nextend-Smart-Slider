<?php


namespace Nextend\SmartSlider3\Slider;


use Nextend\Framework\Asset\Predefined;
use Nextend\SmartSlider3\Renderable\AbstractRenderable;
use Nextend\SmartSlider3\Settings;
use Nextend\SmartSlider3\Slider\Feature\Align;
use Nextend\SmartSlider3\Slider\Feature\Autoplay;
use Nextend\SmartSlider3\Slider\Feature\BlockRightClick;
use Nextend\SmartSlider3\Slider\Feature\Controls;
use Nextend\SmartSlider3\Slider\Feature\FadeOnLoad;
use Nextend\SmartSlider3\Slider\Feature\Focus;
use Nextend\SmartSlider3\Slider\Feature\LayerMode;
use Nextend\SmartSlider3\Slider\Feature\LazyLoad;
use Nextend\SmartSlider3\Slider\Feature\MaintainSession;
use Nextend\SmartSlider3\Slider\Feature\Margin;
use Nextend\SmartSlider3\Slider\Feature\Optimize;
use Nextend\SmartSlider3\Slider\Feature\PostBackgroundAnimation;
use Nextend\SmartSlider3\Slider\Feature\Responsive;
use Nextend\SmartSlider3\Slider\Feature\SlideBackground;
use Nextend\SmartSlider3\Slider\Feature\Spinner;
use Nextend\SmartSlider3\Slider\Feature\TranslateUrl;

class FeatureManager {

    /**
     * @var AbstractRenderable
     */
    private $slider;

    /**
     * @var FadeOnLoad
     */
    public $fadeOnLoad;

    /**
     * @var Responsive
     */
    public $responsive;

    /**
     * @var Controls
     */
    public $controls;

    /**
     * @var LazyLoad
     */
    public $lazyLoad;

    /**
     * @var Align
     */
    public $align;

    /**
     * @var BlockRightClick
     */
    public $blockRightClick;
    /**
     * @var Autoplay
     */
    public $autoplay;

    /**
     * @var TranslateUrl
     */
    public $translateUrl;

    /**
     * @var LayerMode
     */
    public $layerMode;

    /**
     * @var SlideBackground
     */
    public $slideBackground;

    /**
     * @var PostBackgroundAnimation
     */
    public $postBackgroundAnimation;

    /**
     * @var Focus
     */
    public $focus;

    /**
     * @var Spinner
     */
    public $loadSpinner;

    /**
     * @var MaintainSession
     */
    public $maintainSession;

    /**
     * @var Margin
     */
    public $margin;

    public $optimize;

    /**
     * FeatureManager constructor.
     *
     * @param $slider AbstractRenderable
     */
    public function __construct($slider) {
        $this->slider = $slider;

        $this->optimize        = new Optimize($slider);
        $this->fadeOnLoad      = new FadeOnLoad($slider);
        $this->align           = new Align($slider);
        $this->responsive      = new Responsive($slider, $this);
        $this->controls        = new Controls($slider);
        $this->lazyLoad        = new LazyLoad($slider);
        $this->margin          = new Margin($slider);
        $this->blockRightClick = new BlockRightClick($slider);
        $this->maintainSession = new MaintainSession($slider);
        $this->autoplay        = new Autoplay($slider);
        $this->translateUrl    = new TranslateUrl($slider);
        $this->layerMode       = new LayerMode($slider);
        $this->slideBackground = new SlideBackground($slider);
        $this->focus           = new Focus($slider);
        $this->postBackgroundAnimation = new PostBackgroundAnimation($slider);
    
        $this->loadSpinner = new Spinner($slider);
    }

    public function generateJSProperties() {

        $return         = array(
            'admin'                   => $this->slider->isAdmin,
            'callbacks'               => $this->slider->params->get('callbacks', ''),
            'background.video.mobile' => intval($this->slider->params->get('slides-background-video-mobile', 1))
        );
        $randomizeCache = $this->slider->params->get('randomize-cache', 0);
        if (!$this->slider->isAdmin && $randomizeCache) {
            $return['randomize'] = array(
                'randomize'      => intval($this->slider->params->get('randomize', 0)),
                'randomizeFirst' => intval($this->slider->params->get('randomizeFirst', 0))
            );
        }
        $enabled = $this->slider->params->get('global-lightbox', 0);
        if ($enabled) {
            Predefined::loadLiteBox();

            $this->slider->exposeSlideData['lightboxImage'] = true;

            $label                         = $this->slider->params->get('global-lightbox-label', 0);
            $return['plugins']['lightbox'] = array(
                'label' => $label
            );
            switch ($label) {
                case 'namemore':
                    $this->slider->exposeSlideData['description'] = true;
                    break;
            }
        }
    

        $return['alias'] = array(
            'id'           => intval($this->slider->params->get('alias-id', 0)),
            'smoothScroll' => intval($this->slider->params->get('alias-smoothscroll', 0)),
            'slideSwitch'  => intval($this->slider->params->get('alias-slideswitch', 0)),
            'scroll'       => intval($this->slider->params->get('alias-slideswitch-scroll', 1))
        );

        $this->makeJavaScriptProperties($return);

        return $return;
    }

    protected function makeJavaScriptProperties(&$properties) {
        $this->align->makeJavaScriptProperties($properties);
        $this->fadeOnLoad->makeJavaScriptProperties($properties);
        $this->responsive->makeJavaScriptProperties($properties);
        $this->controls->makeJavaScriptProperties($properties);
        $this->lazyLoad->makeJavaScriptProperties($properties);
        $this->blockRightClick->makeJavaScriptProperties($properties);
        $this->maintainSession->makeJavaScriptProperties($properties);
        $this->autoplay->makeJavaScriptProperties($properties);
        $this->layerMode->makeJavaScriptProperties($properties);
        $this->slideBackground->makeJavaScriptProperties($properties);
        $this->focus->makeJavaScriptProperties($properties);
        $this->postBackgroundAnimation->makeJavaScriptProperties($properties);
    
        $properties['initCallbacks'] = &$this->slider->initCallbacks;
    }

    /**
     * @param $slide Slide
     */
    public function makeSlide($slide) {
        $this->postBackgroundAnimation->makeSlide($slide);
    
    }

    /**
     * @param $slide Slide
     *
     * @return string
     */
    public function makeBackground($slide) {

        return $this->slideBackground->make($slide);
    }

    public function addInitCallback($callback, $name = false) {
        $this->slider->addScript($callback, $name);
    }
}