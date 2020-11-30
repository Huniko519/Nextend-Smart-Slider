<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\ImageArea;


use Nextend\Framework\Form\Element\LayerWindowFocus;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\HiddenText;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemImageArea extends AbstractItem {

    protected $ordering = 6;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'imagearea';
    }

    public function getTitle() {
        return n2_('Image area');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--imagearea';
    }

    public function getGroup() {
        return n2_x('Advanced', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemImageAreaFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {
        return parent::getValues() + array(
                'image'       => '$ss3-frontend$/images/placeholder/image.png',
                'href'        => '#',
                'href-target' => '_self',
                'href-rel'    => '',
                'fillmode'    => 'cover',
                'positionx'   => 50,
                'positiony'   => 50
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
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', ResourceTranslator::toUrl($data->get('image')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-imagearea', n2_('General'));

        $fieldImage = new FieldImage($settings, 'image', n2_('Image'), '', array(
            'relatedAlt'    => 'item_imagealt',
            'width'         => 220,
            'relatedFields' => array(
                'item_imageareaitem-imagearea-focus'
            )
        ));

        $fieldFocusX = new HiddenText($settings, 'positionx', 50);
        $fieldFocusY = new HiddenText($settings, 'positiony', 50);

        $focusField = new LayerWindowFocus($settings, 'item-imagearea-focus', n2_('Focus'), array(
            'tipLabel'       => n2_('Focus'),
            'tipDescription' => n2_('You can set the starting position of a background image. This makes sure that the selected part will always remain visible, so you should pick the most important part.')
        ));

        $focusField->setFields($fieldImage, $fieldFocusX, $fieldFocusY);

        new Select($settings, 'fillmode', n2_('Fill mode'), 'cover', array(
            'options' => array(
                'cover'   => n2_('Fill'),
                'contain' => n2_('Fit')
            )
        ));


        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-imagearea-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'relatedFields' => array(
                'item_imageareahref-target',
                'item_imageareahref-rel'
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