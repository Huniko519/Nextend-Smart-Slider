<?php


namespace Nextend\SmartSlider3\Renderable\Item\Heading;


use Nextend\Framework\Misc\Base64;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemHeadingFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $attributes = array();
        $inDelay  = max(0, intval($this->data->get('split-text-delay-in', 0))) / 1000;
        $outDelay = max(0, intval($this->data->get('split-text-delay-out', 0))) / 1000;

        $in  = $this->data->get('split-text-animation-in', '');
        $out = $this->data->get('split-text-animation-out', '');

        $transformOrigin    = implode('% ', explode('|*|', $this->data->get('split-text-transform-origin', '50|*|50|*|0'))) . 'px';
        $backfaceVisibility = $this->data->get('split-text-backface-visibility', 1) ? 'visible' : 'hidden';

        if (!empty($in) || !empty($out)) {
            if ($this->isEditor && $owner->underEdit) {
                $owner->addScript('new N2Classes.HeadingItemSplitTextAdmin(this, "' . $this->id . '", "' . $transformOrigin . '", "' . $backfaceVisibility . '",  "' . $in . '",' . $inDelay . ', "' . $out . '", ' . $outDelay . ');');
            } else {

                if (!empty($in)) {
                    $in = Base64::decode($in);
                } else {
                    $in = 'false';
                }
                if (!empty($out)) {
                    $out = Base64::decode($out);
                } else {
                    $out = 'false';
                }

                $owner->addScript('new N2Classes.FrontendItemHeadingSplitText(this, "' . $this->id . '", "' . $transformOrigin . '", "' . $backfaceVisibility . '",  ' . $in . ',' . $inDelay . ', ' . $out . ', ' . $outDelay . ');');
            }
        }
    
        $font = $owner->addFont($this->data->get('font'), 'hover');

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $linkAttributes = array(
            'class' => 'n2-ow'
        );
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        $title = $this->data->get('title', '');
        if (!empty($title)) {
            $attributes['title'] = $title;
        }

        $href = $this->data->get('href', '');
        if (!empty($href) && $href != '#') {
            $linkAttributes['class'] .= ' ' . $font . $style;

            $font  = '';
            $style = '';
        }

        $linkAttributes['style'] = "display:" . ($this->data->get('fullwidth', 1) ? 'block' : 'inline-block') . ";";

        $allowedTags  = '<a><span><sub><sup><em><i><var><cite><b><strong><small><bdo>';
        $strippedHtml = strip_tags($owner->fill($this->data->get('heading', '')), $allowedTags);

        return $this->heading($this->data->get('priority', 'div'), $attributes + array(
                "id"    => $this->id,
                "class" => $font . $style . " " . $owner->fill($this->data->get('class', '')) . ' n2-ss-item-content n2-ow',
                "style" => "display:" . ($this->data->get('fullwidth', 1) ? 'block' : 'inline-block') . ";" . ($this->data->get('nowrap', 0) ? 'white-space:nowrap;' : '')
            ), $this->getLink(str_replace("\n", '<br />', $strippedHtml), $linkAttributes));
    }

    private function heading($type, $attributes, $content) {
        if ($type > 0) {
            return Html::tag("h{$type}", $attributes, $content);
        }

        return Html::tag("div", $attributes, $content);
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }
}