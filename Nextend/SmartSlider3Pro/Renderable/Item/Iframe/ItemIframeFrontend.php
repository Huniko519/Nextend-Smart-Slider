<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Iframe;


use Nextend\Framework\Parser\Common;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemIframeFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $size = (array)Common::parse($this->data->get('size', ''));
        if (!isset($size[0])) $size[0] = '100%';
        if (!isset($size[1])) $size[1] = '100%';

        $attributes = array(
            "encode"      => false,
            "frameborder" => 0,
            "class"       => "n2-ow intrinsic-ignore",
            "width"       => $size[0],
            "height"      => $size[1],
            "scrolling"   => $this->data->get("scroll"),
            "sandbox"     => 'allow-modals allow-forms allow-popups allow-scripts allow-same-origin'
        );

        $title = $this->data->get('title', '');
        if (!empty($title)) {
            $attributes['title'] = $title;
        }

        $attributes[$owner->isLazyLoadingEnabled() ? 'data-lazysrc' : 'src'] = $owner->fill($this->data->get("url"));

        return Html::tag('div', array('class' => 'n2-ss-item-iframe-wrapper n2-ss-item-content n2-ow'), Html::tag("iframe", $attributes, ""));
    }

    public function needHeight() {
        return true;
    }
}