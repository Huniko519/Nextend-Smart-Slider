<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Icon;


use Nextend\Framework\Form\Element\Hidden\HiddenStyle;
use Nextend\Framework\Form\Element\Icon;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemIcon extends AbstractItem {

    protected $ordering = 5;

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'icon2';
    }

    public function getTitle() {
        return n2_('Icon');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--icon';
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemIconFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {
        return parent::getValues() + array(
                'icon'        => 'fa:smile-o',
                'color'       => 'ffffffff',
                'colorhover'  => 'ffffff00',
                'size'        => 100,
                'href'        => '#',
                'href-target' => '_self',
                'href-rel'    => '',
                'style'       => ''
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

        $data->set('icon', $slide->fill($data->get('icon', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-icon2-icon', n2_('General'));
        new Icon($settings, 'icon', n2_('Icon'));
        new Color($settings, 'color', n2_('Color'), '00000080', array(
            'alpha' => true
        ));
        new Color($settings, 'colorhover', n2_('Hover color'), '00000000', array(
            'alpha' => true
        ));

        new NumberSlider($settings, 'size', n2_('Size'), 24, array(
            'min'       => 4,
            'max'       => 10000,
            'sliderMax' => 200,
            'step'      => 4,
            'wide'      => 3,
            'unit'      => 'px'
        ));

        new HiddenStyle($settings, 'style', false, '', array(
            'mode' => 'box'
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-icon2-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'style'         => 'width:236px;',
            'relatedFields' => array(
                'item_icon2href-target',
                'item_icon2href-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));
    }
}