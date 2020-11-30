<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\HtmlList;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Hidden\HiddenStyle;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemHtmlList extends AbstractItem {

    protected $ordering = 6;

    protected $layerProperties = array(
        "desktopportraitleft"   => 0,
        "desktopportraittop"    => 0,
        "desktopportraitwidth"  => 400,
        "desktopportraitalign"  => "left",
        "desktopportraitvalign" => "top"
    );

    protected $fonts = array(
        'font' => array(
            'defaultName' => 'item-list-font',
            'value'       => '{"data":[{"color":"ffffffff","size":"14||px","align":"left"},{"color":"1890d7ff"}]}'
        )
    );

    protected $styles = array(
        'liststyle' => array(
            'defaultName' => 'item-list-liststyle',
            'value'       => '{"data":[{"extra":"margin-top:0;\nmargin-bottom:0;"}]}'
        ),
        'itemstyle' => array(
            'defaultName' => 'item-list-itemstyle',
            'value'       => '{"name":"List","data":[{"padding":"10|*|20|*|10|*|20|*|px","extra":"margin:0;"}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'list';
    }

    public function getTitle() {
        return n2_('List');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--list';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemHtmlListFrontend($this, $id, $itemData, $layer);
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Font($row1, 'item-list-font', n2_('List'), $this->fonts['font']['value'], array(
            'mode' => 'list'
        ));

        new Style($row1, 'item-list-liststyle', n2_('List'), $this->styles['liststyle']['value'], array(
            'mode' => 'heading'
        ));

        new Style($row1, 'item-list-itemstyle', n2_('Item'), $this->styles['itemstyle']['value'], array(
            'mode' => 'heading'
        ));
    }

    public function getValues() {

        return parent::getValues() + array(
                'content' => n2_("Item 1\nItem 2\nItem 3"),
                'type'    => 'disc'
            );
    }


    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('content', $slide->fill($data->get('content', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('liststyle'));
        $export->addVisual($data->get('itemstyle'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('liststyle', $import->fixSection($data->get('liststyle')));
        $data->set('itemstyle', $import->fixSection($data->get('itemstyle')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-list', n2_('General'));

        new Textarea($settings, 'content', n2_('Items'), '', array(
            'width'  => 314,
            'height' => 120
        ));

        new Select($settings, 'type', n2_('List type'), '', array(
            'options' => array(
                'none'                 => n2_x('None', 'List layer type'),
                'disc'                 => n2_x('Disc', 'List layer type'),
                'square'               => n2_x('Square', 'List layer type'),
                'circle'               => n2_x('Circle', 'List layer type'),
                'decimal'              => n2_x('Decimal', 'List layer type'),
                'armenian'             => n2_x('Armenian', 'List layer type'),
                'cjk-ideographic'      => n2_x('Cjk-ideographic', 'List layer type'),
                'decimal-leading-zero' => n2_x('Decimal-leading-zero', 'List layer type'),
                'georgian'             => n2_x('Georgian', 'List layer type'),
                'hebrew'               => n2_x('Hebrew', 'List layer type'),
                'hiragana'             => n2_x('Hiragana', 'List layer type'),
                'hiragana-iroha'       => n2_x('Hiragana-iroha', 'List layer type'),
                'katakana'             => n2_x('Katakana', 'List layer type'),
                'katakana-iroha'       => n2_x('Katakana-iroha', 'List layer type'),
                'lower-alpha'          => n2_x('Lower-alpha', 'List layer type'),
                'lower-greek'          => n2_x('Lower-greek', 'List layer type'),
                'lower-latin'          => n2_x('Lower-latin', 'List layer type'),
                'lower-roman'          => n2_x('Lower-roman', 'List layer type'),
                'upper-alpha'          => n2_x('Upper-alpha', 'List layer type'),
                'upper-latin'          => n2_x('Upper-latin', 'List layer type'),
                'upper-roman'          => n2_x('Upper-roman', 'List layer type')
            )
        ));
        new HiddenFont($settings, 'font', false, '', array(
            'mode' => 'list',
        ));
        new HiddenStyle($settings, 'liststyle', n2_('Style') . ' - ' . n2_('List'), '', array(
            'mode' => 'heading'
        ));
        new HiddenStyle($settings, 'itemstyle', n2_('Style') . ' - ' . n2_('Item'), '', array(
            'mode' => 'heading'
        ));
    }
}