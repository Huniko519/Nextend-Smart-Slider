<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\ImageArea;


use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemImageAreaFrontend extends AbstractItemFrontend {

    public function render() {

        if ($this->hasLink()) {
            return $this->getLink($this->getHtml(false), array(
                'style' => 'display: block; width:100%;height:100%;',
                'class' => 'n2-ss-item-content n2-ow'
            ));
        }

        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml($isContent = true) {
        $owner = $this->layer->getOwner();

        $image = $this->data->get('image', '');
        if (empty($image)) {
            return '';
        }

        $fixedImageUrl = ResourceTranslator::toUrl($owner->fill($image));

        $owner->addImage($fixedImageUrl);

        return Html::tag('span', array(
            'class' => ($isContent ? 'n2-ss-item-content ' : '') . 'n2-ow',
            'style' => 'display:inline-block;vertical-align:top;width:100%;height:100%;background: URL(' . $fixedImageUrl . ') no-repeat;background-size:' . $this->data->get('fillmode', 'cover') . ';background-position: ' . $this->data->get('positionx', 50) . '% ' . $this->data->get('positiony', 50) . '%;'
        ));
    }

    public function needWidth() {
        return true;
    }

    public function needHeight() {
        return true;
    }
}