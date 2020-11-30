<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Area;


use Nextend\Framework\Form\Element\MarginPadding;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Gradient;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemArea extends AbstractItem {

    protected $ordering = 100;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 150,
        "desktopportraitheight" => 150
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'area';
    }

    public function getTitle() {
        return n2_('Area');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--area';
    }

    public function getGroup() {
        return n2_x('Advanced', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemAreaFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {
        return parent::getValues() + array(
                'width'        => '',
                'height'       => '',
                'color'        => '000000ff',
                'gradient'     => 'off',
                'color2'       => '000000ff',
                'css'          => '',
                'borderWidth'  => '0|*|0|*|0|*|0',
                'borderStyle'  => 'solid',
                'borderColor'  => 'ffffff1f',
                'borderRadius' => 0,
                'href'         => '#',
                'href-target'  => '_self',
                'href-rel'     => '',
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

        $borderWidthV1 = $data->get('borderWidth', 0);
        if (is_numeric($borderWidthV1)) {
            $data->set('borderWidth', $borderWidthV1 . '|*|' . $borderWidthV1 . '|*|' . $borderWidthV1 . '|*|' . $borderWidthV1 . '');
        }

    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function renderFields($container) {
        $color = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-area', n2_('Color'));
        new Color($color, 'color', n2_('Background color'), '00000000', array(
            'alpha' => true
        ));
        new Gradient($color, 'gradient', n2_('Gradient'), 'off', array(
            'relatedFields' => array(
                'item_areacolor2'
            )
        ));
        new Color($color, 'color2', n2_('Color end'), 'ffffff00', array(
            'alpha' => true
        ));

        $border = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-area-border', n2_('Border'));

        $borderWidth = new MarginPadding($border, 'borderWidth', n2_('Border'), '0|*|0|*|0|*|0', array(
            'unit'          => 'px',
            'relatedFields' => array(
                'item_areaborderStyle',
                'item_areaborderColor',
            )
        ));

        for ($i = 1; $i < 5; $i++) {
            new NumberAutoComplete($borderWidth, 'borderWidth-' . $i, false, '', array(
                'values' => array(
                    0,
                    1,
                    2,
                    3,
                    5
                ),
                'min'    => 0,
                'wide'   => 3
            ));
        }

        new Select($border, 'borderStyle', n2_('Style'), 'solid', array(
            'options' => array(
                'none'   => n2_('None'),
                'solid'  => n2_('Solid'),
                'dashed' => n2_('Dashed'),
                'dotted' => n2_('Dotted'),
            )
        ));

        new Color($border, 'borderColor', n2_('Color'), '00000000', array(
            'alpha' => true
        ));

        new NumberAutoComplete($border, 'borderRadius', n2_('Border radius'), 0, array(
            'values' => array(
                0,
                3,
                5,
                10,
                99
            ),
            'wide'   => 3,
            'unit'   => 'px'
        ));

        $size = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-area-size', n2_('Size'));
        new Number($size, 'width', n2_('Width'), '', array(
            'wide'           => 4,
            'unit'           => 'px',
            'tipLabel'       => n2_('Width'),
            'tipDescription' => sprintf(n2_('Fix width for the %1$s.'), $this->getTitle())
        ));
        new Number($size, 'height', n2_('Height'), '', array(
            'wide'           => 4,
            'unit'           => 'px',
            'tipLabel'       => n2_('Height'),
            'tipDescription' => sprintf(n2_('Fix height for the %1$s.'), $this->getTitle())
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-area-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'relatedFields' => array(
                'item_areahref-target',
                'item_areahref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $developer = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-area-developer', n2_('Advanced'));
        new Textarea($developer, 'css', 'CSS', '', array(
            'width'          => 314,
            'tipLabel'       => 'CSS',
            'tipDescription' => n2_('Write custom CSS codes here without selectors.')
        ));
    }
}