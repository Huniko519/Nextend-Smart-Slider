<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Transition;


use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemTransitionFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {

        $image  = $this->data->get('image', '');
        $image2 = $this->data->get('image2', '');
        if (empty($image) && empty($image2)) {
            return '';
        }

        $owner = $this->layer->getOwner();

        $this->loadResources($owner);
        $owner->addScript('new N2Classes.FrontendItemTransition(this, "' . $this->id . '", "' . $this->data->get('animation', 'Fade') . '");');

        $html = Html::openTag("div", array(
            "class" => "n2-ss-item-transition-inner n2-ss-img-wrapper n2-ow"
        ));
        $html .= Html::tag('img', Html::addExcludeLazyLoadAttributes($owner->optimizeImage($image) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt', ''))),
                'class' => 'n2-ss-item-transition-image1 n2-ow'
            )), false);
        $html .= Html::tag('img', Html::addExcludeLazyLoadAttributes($owner->optimizeImage($image2) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt2', ''))),
                'class' => 'n2-ss-item-transition-image2 n2-ow'
            )), false);
        $html .= Html::closeTag('div');

        $linkAttributes = array('class' => 'n2-ow');
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return Html::tag("div", array(
            "id"    => $this->id,
            "class" => "n2-ss-item-transition n2-ss-item-content n2-ow"
        ), $this->getLink($html, $linkAttributes));
    }

    /**
     * @param $owner AbstractRenderableOwner
     */
    public function loadResources($owner) {

        $owner->addLess(self::getAssetsPath() . "/transition.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}