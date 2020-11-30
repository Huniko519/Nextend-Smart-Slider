<?php


namespace Nextend\SmartSlider3Pro\Widget;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Widget\Arrow\ArrowImage\ArrowImage;
use Nextend\SmartSlider3\Widget\Autoplay\AutoplayImage\AutoplayImage;
use Nextend\SmartSlider3\Widget\Bullet\BulletTransition\BulletTransition;
use Nextend\SmartSlider3\Widget\Group\AbstractWidgetGroup;
use Nextend\SmartSlider3\Widget\Thumbnail\Basic\ThumbnailBasic;
use Nextend\SmartSlider3Pro\Widget\Arrow\ArrowGrow\ArrowGrow;
use Nextend\SmartSlider3Pro\Widget\Arrow\ArrowImageBar\ArrowImageBar;
use Nextend\SmartSlider3Pro\Widget\Arrow\ArrowReveal\ArrowReveal;
use Nextend\SmartSlider3Pro\Widget\Arrow\ArrowText\ArrowText;
use Nextend\SmartSlider3Pro\Widget\Bar\BarVertical\BarVertical;
use Nextend\SmartSlider3Pro\Widget\Bullet\BulletNumbers\BulletNumbers;
use Nextend\SmartSlider3Pro\Widget\Bullet\BulletText\BulletText;
use Nextend\SmartSlider3Pro\Widget\FullScreen\FullScreenImage\FullScreenImage;
use Nextend\SmartSlider3Pro\Widget\Group\FullScreen;
use Nextend\SmartSlider3Pro\Widget\Group\Html;
use Nextend\SmartSlider3Pro\Widget\Group\Indicator;
use Nextend\SmartSlider3Pro\Widget\Html\HtmlCode\HtmlCode;
use Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorPie\IndicatorPie;
use Nextend\SmartSlider3Pro\Widget\Indicator\IndicatorStripe\IndicatorStripe;

class WidgetLoader {

    public function __construct() {

        Plugin::addAction('PluggableFactorySliderWidgetGroup', array(
            $this,
            'sliderWidgetGroup'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetArrow', array(
            $this,
            'sliderWidgetArrow'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetBullet', array(
            $this,
            'sliderWidgetBullet'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetAutoplay', array(
            $this,
            'sliderWidgetAutoplay'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetBar', array(
            $this,
            'sliderWidgetBar'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetIndicator', array(
            $this,
            'sliderWidgetIndicator'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetHtml', array(
            $this,
            'sliderWidgetHtml'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetFullScreen', array(
            $this,
            'sliderWidgetFullScreen'
        ));

        Plugin::addAction('PluggableFactorySliderWidgetThumbnail', array(
            $this,
            'sliderWidgetThumbnail'
        ));
    }

    public function sliderWidgetGroup() {
        new FullScreen();
        new Indicator();
        new Html();
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetArrow($group) {

        new ArrowImage($group, 'image');

        new ArrowImage($group, 'imageBigRectangle', array(
            'widget-arrow-style'                    => '{"data":[{"backgroundcolor":"000000ab","padding":"20|*|20|*|20|*|20|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""},{"backgroundcolor":"5F39C2FF"}]}',
            'widget-arrow-animation'                => 'horizontal',
            'widget-arrow-previous-position-offset' => 0,
            'widget-arrow-next-position-offset'     => 0,
        ));

        new ArrowImage($group, 'imageVertical', array(
            'widget-arrow-previous'               => '$ss$/plugins/widgetarrow/image/image/previous/simple-vertical.svg',
            'widget-arrow-next'                   => '$ss$/plugins/widgetarrow/image/image/next/simple-vertical.svg',
            'widget-arrow-style'                  => '{"data":[{"backgroundcolor":"000000ab","padding":"10|*|10|*|10|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"3","extra":""},{"backgroundcolor":"04C018FF"}]}',
            'widget-arrow-previous-position-area' => 3,
            'widget-arrow-next-position-area'     => 10,
        ));

        new ArrowGrow($group, 'grow');

        new ArrowImageBar($group, 'imagebar');

        new ArrowReveal($group, 'reveal');

        new ArrowText($group, 'text');
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetAutoplay($group) {
        new AutoplayImage($group, 'imageBlue', array(
            'widget-autoplay-position-area' => 11,
            'widget-autoplay-style'         => '{"data":[{"backgroundcolor":"000000ab","padding":"10|*|10|*|10|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"3","extra":""},{"backgroundcolor":"04C018FF"}]}'
        ));
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetHtml($group) {

        new HtmlCode($group, 'html');
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetFullScreen($group) {

        new FullScreenImage($group, 'image');

        new FullScreenImage($group, 'imageBlue', array(
            'widget-fullscreen-tonormal' => '$ss$/plugins/widgetfullscreen/image/image/tonormal/full2.svg',
            'widget-fullscreen-tofull'   => '$ss$/plugins/widgetfullscreen/image/image/tofull/full2.svg',
            'widget-fullscreen-style'    => '{"data":[{"backgroundcolor":"000000ab","padding":"10|*|10|*|10|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"3","extra":""},{"backgroundcolor":"04C018FF"}]}'
        ));
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetBullet($group) {

        new BulletTransition($group, 'transitionBorder', array(
            'widget-bullet-style' => '{"data":[{"backgroundcolor":"00000000","padding":"5|*|5|*|5|*|5|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"2|*|solid|*|000000c2","borderradius":"50","extra":"margin: 4px;"},{"backgroundcolor":"000000ba","border":"2|*|solid|*|ffffff00"}]}'
        ));

        new BulletTransition($group, 'transitionRectangle', array(
            'widget-bullet-style' => '{"data":[{"backgroundcolor":"000000ab","padding":"8|*|8|*|8|*|8|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":"margin: 4px;"},{"backgroundcolor":"04C018FF"}]}'
        ));

        new BulletTransition($group, 'transitionBar', array(
            'widget-bullet-style' => '{"data":[{"backgroundcolor":"00000000","padding":"5|*|5|*|5|*|5|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"2|*|solid|*|000000c2","borderradius":"50","extra":"margin: 4px 3px;"},{"backgroundcolor":"000000ba","border":"2|*|solid|*|ffffff00"}]}',
            'widget-bullet-bar'   => '{"data":[{"backgroundcolor":"ffffff80","padding":"2|*|5|*|2|*|5|*|px","boxshadow":"0|*|1|*|5|*|0|*|00000033","border":"0|*|solid|*|000000ff","borderradius":"50","extra":""}]}',
        ));

        new BulletNumbers($group, 'numbers');

        new BulletText($group, 'text');
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetIndicator($group) {

        new IndicatorPie($group, 'pie');

        new IndicatorPie($group, 'pieFull', array(
            'widget-indicator-thickness' => 100,
            'widget-indicator-track'     => 'ffffff00',
            'widget-indicator-bar'       => 'ffffff80',
        ));

        new IndicatorStripe($group, 'stripe');
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetBar($group) {

        new BarVertical($group, 'vertical');
    }

    /**
     * @param AbstractWidgetGroup $group
     */
    public function sliderWidgetThumbnail($group) {

        new ThumbnailBasic($group, 'defaultHorizontal', array(
            'widget-thumbnail-style-bar'         => '{"data":[{"backgroundcolor":"242424ff","padding":"0|*|0|*|0|*|0|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""}]}',
            'widget-thumbnail-style-slides'      => '{"data":[{"backgroundcolor":"00000000","padding":"0|*|0|*|0|*|0|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|ffffff00","borderradius":"0","opacity":"40","extra":"transition: all 0.4s;\nbackground-size: cover;"},{"border":"0|*|solid|*|ffffffcc","opacity":"100","extra":""}]}',
            'widget-thumbnail-title-style'       => '{"data":[{"backgroundcolor":"00000000","padding":"3|*|10|*|3|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":"bottom: 0;\nleft: 0;"}]}',
            'widget-thumbnail-title'             => 1,
            'widget-thumbnail-title-font'        => '{"data":[{"color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ab","afont":"Montserrat","lineheight":"1.4","bold":0,"italic":0,"underline":0,"align":"left"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
            'widget-thumbnail-description'       => 1,
            'widget-thumbnail-caption-placement' => 'after'
        ));

        new ThumbnailBasic($group, 'defaultHorizontalGallery', array(
            'widget-thumbnail-title' => 0,
            'widget-thumbnail-group' => 2
        ));

        new ThumbnailBasic($group, 'defaultVertical', array(
            'widget-thumbnail-position-area' => 5,
            'widget-thumbnail-title'         => 1,
        ));

        new ThumbnailBasic($group, 'defaultVerticalText', array(
            'widget-thumbnail-position-area'     => 5,
            'widget-thumbnail-style-slides'      => '{"data":[{"backgroundcolor":"00000000","padding":"0|*|0|*|0|*|0|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|ffffff00","borderradius":"0","opacity":"60","extra":"background-size: cover;\nmargin: 10px 0;\n"},{"backgroundcolor":"00000000","opacity":"100","extra":"background-size: cover;\nmargin: 10px 0;\n"}]}',
            'widget-thumbnail-title-style'       => '{"data":[{"backgroundcolor":"00000000","padding":"3|*|10|*|3|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":"bottom: 0;\nleft: 0;"}]}',
            'widget-thumbnail-title'             => 1,
            'widget-thumbnail-title-font'        => '{"data":[{"color":"ffffffe6","size":"14||px","tshadow":"0|*|0|*|0|*|000000ab","afont":"Montserrat","lineheight":"1.8","bold":0,"italic":0,"underline":0,"align":"left","extra":""},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
            'widget-thumbnail-description'       => 1,
            'widget-thumbnail-description-font'  => '{"data":[{"color":"ffffff7d","size":"12||px","tshadow":"0|*|0|*|0|*|000000ab","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
            'widget-thumbnail-caption-size'      => 200,
            'widget-thumbnail-show-image'        => 0,
            'widget-thumbnail-width'             => 100,
            'widget-thumbnail-height'            => 60,
            'widget-thumbnail-caption-placement' => 'after'
        ));
    }

}