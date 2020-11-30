<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\ImageBox;


use Nextend\Framework\Icon\Icon;
use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemImageBoxFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $layout = $this->data->get('layout');

        $attr = array(
            'class'             => 'n2-ss-item-imagebox-container n2-ss-item-content n2-ow-all ' . $style . ($this->data->get('fullwidth', 1) ? ' n2-ss-fullwidth' : ''),
            'data-layout'       => $layout,
            'data-csstextalign' => $this->data->get('inneralign')
        );
        if ($layout == 'left' || $layout == 'right') {
            $attr['data-verticalalign'] = $this->data->get('verticalalign');
        }

        $html = Html::openTag('div', $attr);

        // START IMAGE SECTION
        $imageHTML           = '';
        $imageContainerStyle = '';
        $imageInnerStyle     = '';
        $icon                = $this->data->get('icon');
        $image               = $this->data->get('image');
        $imageType           = $this->data->get('imagetype', 'icon');
        if (!empty($icon) && $imageType == 'icon') {
            $iconData  = Icon::render($icon);
            $imageHTML .= Html::tag('i', array(
                'class' => 'n2i ' . $iconData['class'],
                'style' => 'color: ' . Color::colorToRGBA($this->data->get('iconcolor')) . ';font-size:' . ($this->data->get('iconsize') / 16 * 100) . '%'
            ), $iconData['ligature']);
        } else if (!empty($image)) {

            if ($layout == 'top' || $layout == 'bottom') {
                $imageInnerStyle .= 'max-width:' . $this->data->get('imagewidth') . '%;';
            } else {
                $imageContainerStyle .= 'max-width:' . $this->data->get('imagewidth') . '%;';
            }

            $fixedImageUrl = ResourceTranslator::toUrl($owner->fill($this->data->get('image')));

            $owner->addImage($fixedImageUrl);

            $imageHTML .= Html::image($fixedImageUrl, $owner->fill($this->data->get('alt')), Html::addExcludeLazyLoadAttributes(array(
                'style' => $imageInnerStyle,
                'class' => ''
            )));
        }

        if (!empty($imageHTML)) {
            $html .= Html::tag('div', array(
                'class' => 'n2-ss-item-imagebox-image n2-ow',
                'style' => $imageContainerStyle
            ), $this->getLink($imageHTML));
        }
        // END IMAGE SECTION


        // START CONTENT SECTION
        $html .= Html::openTag('div', array(
            'class' => 'n2-ss-item-imagebox-content n2-ow',
            'style' => 'padding:' . implode('px ', explode('|*|', $this->data->get('padding'))) . 'px'
        ));

        $heading = $this->data->get('heading');
        if (!empty($heading)) {
            $font = $owner->addFont($this->data->get('fonttitle'), 'hover');

            $priority = $this->data->get('headingpriority');
            $html     .= $this->getLink(Html::tag($priority > 0 ? 'h' . $priority : $priority, array('class' => $font), $owner->fill($heading)));
        }

        $description = $this->data->get('description');
        if (!empty($description)) {
            $font = $owner->addFont($this->data->get('fontdescription'), 'paragraph');

            $html .= Html::tag('div', array('class' => $font), $owner->fill($description));
        }

        $html .= '</div>';
        // END CONTENT SECTION


        $html .= '</div>';

        return $html;
    }

    /**
     * @param AbstractRenderableOwner $owner
     */
    public function loadResources($owner) {
        $owner->addLess(self::getAssetsPath() . "/imagebox.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}