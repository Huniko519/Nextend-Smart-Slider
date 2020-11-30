<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\HtmlList;


use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemHtmlListFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }


    private function getHTML() {
        $owner = $this->layer->getOwner();

        $font      = $owner->addFont($this->data->get('font'), 'list');
        $listStyle = $owner->addStyle($this->data->get('liststyle'), 'heading');
        $itemStyle = $owner->addStyle($this->data->get('itemstyle'), 'heading');


        $html = '';
        $lis  = explode("\n", $owner->fill($this->data->get('content', '')));
        foreach ($lis AS $li) {
            $html .= '<li class="' . $itemStyle . ' n2-ow" style="list-style-type:inherit;">' . $li . '</li>';
        }

        return Html::tag('ol', array(
            'class' => $font . '' . $listStyle . ' n2-ss-item-content n2-ow',
            'style' => "list-style-type:" . $this->data->get('type')
        ), $html);
    }
}