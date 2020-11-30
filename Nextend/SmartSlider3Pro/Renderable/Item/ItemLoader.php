<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item;


use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Renderable\Item\ItemFactory;
use Nextend\SmartSlider3Pro\Renderable\Item\AnimatedHeading\ItemAnimatedHeading;
use Nextend\SmartSlider3Pro\Renderable\Item\Area\ItemArea;
use Nextend\SmartSlider3Pro\Renderable\Item\Audio\ItemAudio;
use Nextend\SmartSlider3Pro\Renderable\Item\Caption\ItemCaption;
use Nextend\SmartSlider3Pro\Renderable\Item\CircleCounter\ItemCircleCounter;
use Nextend\SmartSlider3Pro\Renderable\Item\Counter\ItemCounter;
use Nextend\SmartSlider3Pro\Renderable\Item\HighlightedHeading\ItemHighlightedHeading;
use Nextend\SmartSlider3Pro\Renderable\Item\Html\ItemHtml;
use Nextend\SmartSlider3Pro\Renderable\Item\HtmlList\ItemHtmlList;
use Nextend\SmartSlider3Pro\Renderable\Item\Icon\ItemIcon;
use Nextend\SmartSlider3Pro\Renderable\Item\Iframe\ItemIframe;
use Nextend\SmartSlider3Pro\Renderable\Item\ImageArea\ItemImageArea;
use Nextend\SmartSlider3Pro\Renderable\Item\ImageBox\ItemImageBox;
use Nextend\SmartSlider3Pro\Renderable\Item\Input\ItemInput;
use Nextend\SmartSlider3Pro\Renderable\Item\ProgressBar\ItemProgressBar;
use Nextend\SmartSlider3Pro\Renderable\Item\Transition\ItemTransition;
use Nextend\SmartSlider3Pro\Renderable\Item\Video\ItemVideo;
use Nextend\SmartSlider3Pro\Renderable\Joomla\Item\JoomlaModule\ItemJoomlaModule;

class ItemLoader {

    public function __construct() {

        Plugin::addAction('PluggableFactoryRenderableItem', array(
            $this,
            'renderableItems'
        ));
    }

    /**
     * @param ItemFactory $factory
     */
    public function renderableItems($factory) {

        new ItemAnimatedHeading($factory);
        new ItemArea($factory);
        new ItemAudio($factory);
        new ItemCaption($factory);
        new ItemCircleCounter($factory);
        new ItemCounter($factory);
        new ItemHighlightedHeading($factory);
        new ItemHtml($factory);
        new ItemIcon($factory);
        new ItemIframe($factory);
        new ItemImageArea($factory);
        new ItemImageBox($factory);
        new ItemInput($factory);
        new ItemHtmlList($factory);
        new ItemProgressBar($factory);
        new ItemTransition($factory);
        new ItemVideo($factory);
    }
}