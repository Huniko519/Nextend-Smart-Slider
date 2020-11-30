<?php


namespace Nextend\SmartSlider3\Renderable\Item\Button;


use Nextend\Framework\Icon\Icon;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemButtonFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $font = $owner->addFont($this->data->get('font'), 'link');

        $html = Html::openTag("div", array(
            "class" => "n2-ss-button-container n2-ss-item-content n2-ow " . $font . ($this->data->get('fullwidth', 0) ? ' n2-ss-fullwidth' : '') . ($this->data->get('nowrap', 1) ? ' n2-ss-nowrap' : '')
        ));

        $content = '<div>' . $owner->fill($this->data->get("content")) . '</div>';

        $attrs = array();
        $icon = $this->data->get('icon');
        if ($icon) {
            $iconPlacement = $this->data->get('iconplacement', 'left');
            $iconData      = Icon::render($icon);
            if ($iconData) {
                $iconStyle = 'font-size:' . $this->data->get('iconsize') . '%;';
                if ($iconPlacement == 'right') {
                    $iconStyle .= 'margin-left:' . ($this->data->get('iconspacing') / 100) . 'em;';
                } else {
                    $iconStyle .= 'margin-right:' . ($this->data->get('iconspacing') / 100) . 'em;';
                }
                $iconHTML = '<span class="n2i ' . $iconData['class'] . '" style="' . $iconStyle . '">' . $iconData['ligature'] . '</span>';
                if ($iconPlacement == 'right') {
                    $content = $content . $iconHTML;
                } else {
                    $content = $iconHTML . $content;
                }

                $attrs['data-iconplacement'] = $iconPlacement;
            }
        }
    

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $html .= $this->getLink('<div>' . $content . '</div>', $attrs + array(
                "class" => "{$style} n2-ow " . $owner->fill($this->data->get('class', ''))
            ), true);

        $html .= Html::closeTag("div");

        return $html;
    }

    /**
     * @param AbstractRenderableOwner $owner
     */
    public function loadResources($owner) {
        $owner->addLess(self::getAssetsPath() . "/button.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}