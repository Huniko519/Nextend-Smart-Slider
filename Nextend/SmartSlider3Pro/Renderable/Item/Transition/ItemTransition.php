<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Transition;


use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemTransition extends AbstractItem {

    protected $ordering = 5;

    protected $layerProperties = array("desktopportraitwidth" => 200);

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'transition';
    }

    public function getTitle() {
        return n2_('Transition');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--transition';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemTransitionFrontend($this, $id, $itemData, $layer);
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/transition.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function getValues() {
        return parent::getValues() + array(
                'animation'      => 'Fade',
                'image'          => '$ss3-frontend$/images/placeholder/image.png',
                'image2'         => '$ss3-frontend$/images/placeholder/video.png',
                'alt'            => '',
                'alt2'           => '',
                'href'           => '#',
                'href-target'    => '_self',
                'href-rel'       => '',
                'image-optimize' => 1
            );
    }


    public function upgradeData($data) {
        $linkV1 = $data->get('link', '');
        if (!empty($linkV1)) {
            list($link, $target, $rel) = array_pad((array)Common::parse($linkV1), 3, '');
            $data->un_set('link');
            $data->set('href', $link);
            $data->set('href-target', $target);
            $data->set('href-rel', $rel);
        }
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('image2', $slide->fill($data->get('image2', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addImage($data->get('image2'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image', '')));
        $data->set('image2', $import->fixImage($data->get('image2', '')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', ResourceTranslator::toUrl($data->get('image')));
        $data->set('image2', ResourceTranslator::toUrl($data->get('image2')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-transition', n2_('General'));
        new FieldImage($settings, 'image', n2_('Front image'), '', array(
            'relatedAlt' => 'item_transitionalt',
            'width'      => 220
        ));
        new FieldImage($settings, 'image2', n2_('Back image'), '', array(
            'relatedAlt' => 'item_transitionalt2',
            'width'      => 220

        ));

        new Select($settings, 'animation', n2_('Animation'), '', array(
            'options' => array(
                'Fade'           => n2_('Fade'),
                'VerticalFlip'   => n2_('Vertical flip'),
                'HorizontalFlip' => n2_('Horizontal flip')
            )
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-transition-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'relatedFields' => array(
                'item_transitionhref-target',
                'item_transitionhref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $seo = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-transition-seo', n2_('SEO'));
        new Text($seo, 'alt', n2_('Front image alt tag'), '', array(
            'style' => 'width:133px;'
        ));
        new Text($seo, 'alt2', n2_('Back image alt tag'), '', array(
            'style' => 'width:133px;'
        ));

        $optimize = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-transition-optimize', n2_('Optimize'));
        new OnOff($optimize, 'image-optimize', n2_('Optimize image'), 1, array(
            'tipLabel'       => n2_('Optimize image'),
            'tipDescription' => n2_('You can turn off the Layer image optimization for this image, to resize it for tablet and mobile.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1839-caption-layer#optimize'
        ));
    }

}