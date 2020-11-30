<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\HighlightedHeading;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemHighlightedHeadingFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $heading = array();

        $beforeText = $owner->fill($this->data->get('before-text', ''));
        if (!empty($beforeText)) {
            $heading[] = Html::tag('div', array(
                'class' => 'n2-ss-highlighted-heading-before'
            ), $beforeText);
        }

        $highlightedText = $owner->fill($this->data->get('highlighted-text', ''));
        if (!empty($highlightedText)) {

            $svg           = '';
            $highlightType = $this->data->get('type', '');
            if (!empty($highlightType)) {
                $svgPath = self::getAssetsPath() . '/svg/' . $highlightType . '.svg';
                if (Filesystem::fileexists($svgPath)) {
                    $svg = Filesystem::readFile($svgPath);

                    $highlightColor = $this->data->get('color', '');
                    $css            = array(
                        'stroke:#' . substr($highlightColor, 0, 6) . ';',
                        'stroke-opacity:' . Color::hex2opacity($highlightColor) . ';',
                        'stroke-width:' . $this->data->get('width', 10) . 'px;'
                    );
                    $owner->addCSS('div #' . $owner->getElementID() . ' #' . $this->id . ' svg path{' . implode('', $css) . '}');
                }
            }

            $attributes = array(
                'class'          => 'n2-highlighted n2-ss-highlighted-heading-highlighted n2-ow',
                'data-highlight' => $highlightType
            );

            if ($this->data->get('animate', 1)) {
                $attributes['data-animate'] = 1;
            }

            $delay = $this->data->get('delay', 0);
            if ($delay > 0) {
                $attributes['data-delay'] = $delay;
            }

            $duration = $this->data->get('duration', 1500);
            if ($duration != 1500) {
                $attributes['data-duration'] = $duration;
            }

            if ($this->data->get('loop', 0)) {
                $attributes['data-loop'] = 1;
            }

            $loopDelay = $this->data->get('loop-delay', 0);
            if ($loopDelay >= 0) {
                $attributes['data-loop-delay'] = $loopDelay;
            }

            if ($this->data->get('front', 0)) {
                $attributes['data-front'] = 1;
            }

            $highlightedInner = Html::tag('div', array(
                    'class' => 'n2-ss-highlighted-heading-highlighted-text'
                ), $highlightedText) . $svg;


            $href = $this->data->get('href', '');
            if (!empty($href) && $href != '#') {
                $heading[] = $this->getLink($highlightedInner, $attributes);
            } else {
                $heading[] = Html::tag('div', $attributes, $highlightedInner);
            }
        }

        $afterText = $owner->fill($this->data->get('after-text', ''));
        if (!empty($afterText)) {
            $heading[] = Html::tag('div', array(
                'class' => 'n2-ss-highlighted-heading-after'
            ), $afterText);
        }


        $font = $owner->addFont($this->data->get('font'), 'highlight');


        $style = $owner->addStyle($this->data->get('style'), 'highlight');

        return $this->heading($this->data->get('priority', 'div'), array(
            "id"    => $this->id,
            "class" => 'n2-ss-highlighted-heading-wrapper ' . $font . ' ' . $style . ' n2-ss-item-content n2-ow'
        ), implode('', $heading));
    }

    private function heading($type, $attributes, $content) {
        if ($type > 0) {
            return Html::tag("h{$type}", $attributes, $content);
        }

        return Html::tag("div", $attributes, $content);
    }

    /**
     * @param AbstractRenderableOwner $owner
     */
    public function loadResources($owner) {
        $owner->addLess(self::getAssetsPath() . "/highlightedHeading.n2less", array(
            "sliderid" => $owner->getElementID()
        ));

        if (!$owner->isScriptAdded('highlighted-heading')) {
            if ($this->isEditor) {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-currently-edited-slide .n2-ss-highlighted-heading-highlighted\').each((function(i, el){new N2Classes.HighlightedHeadingItemAdmin(el, this)}).bind(this));', 'highlighted-heading');
            } else {
                $owner->addScript('this.sliderElement.find(\'.n2-ss-highlighted-heading-highlighted\').each((function(i, el){new N2Classes.FrontendItemHighlightedHeading(el, this)}).bind(this));', 'highlighted-heading');
            }
        }
    }
}