<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Caption;


use Nextend\Framework\Parser\Color;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemCaptionFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        list($mode, $direction, $scale) = Common::parse($this->data->get('animation', 'Simple|*|left|*|0'));
        $owner->addScript('new N2Classes.FrontendItemCaption(this, "' . $this->id . '", "' . $mode . '", "' . $direction . '", ' . intval($scale) . ');');

        $html = Html::tag('div', array('class' => 'n2-ss-img-wrapper n2-ss-img-crop n2-ow'), Html::tag('img', Html::addExcludeLazyLoadAttributes($owner->optimizeImage($this->data->get('image', '')) + array(
                'alt'   => htmlspecialchars($owner->fill($this->data->get('alt', ''))),
                'class' => 'n2-ow'
            )), false));

        $rgba = Color::colorToRGBA($this->data->get('color', '00000080'));
        $html .= Html::openTag("div", array(
            "class"              => "n2-ss-item-caption-content n2-ow",
            "style"              => "background: {$rgba};",
            "data-verticalalign" => $this->data->get('verticalalign', 'center')
        ));

        $title = $owner->fill($this->data->get('content', ''));
        if ($title != '') {
            $fontTitle = $owner->addFont($this->data->get('fonttitle'), 'paragraph');
            $html      .= Html::tag("div", array("class" => 'n2-ow n2-div-h4 ' . $fontTitle), $title);
        }

        $description = $owner->fill($this->data->get('description', ''));
        if ($description != '') {
            $font = $owner->addFont($this->data->get('font'), 'paragraph');
            $html .= Html::tag("p", array("class" => 'n2-ow ' . $font), $description);
        }

        $html .= Html::closeTag("div");

        $linkAttributes = array(
            'class' => 'n2-ow'
        );
        if ($this->isEditor) {
            $linkAttributes['onclick'] = 'return false;';
        }

        return Html::tag("div", array(
            "id"    => $this->id,
            "class" => "n2-ss-item-caption n2-ss-item-content n2-ow"
        ), $this->getLink($html, $linkAttributes));
    }

    /**
     * @param AbstractRenderableOwner $owner
     */
    public function loadResources($owner) {

        $owner->addLess(self::getAssetsPath() . "/caption.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}