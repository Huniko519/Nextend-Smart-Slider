<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\ProgressBar;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemProgressBar extends AbstractItem {

    protected $ordering = 10;

    protected $layerProperties = array(
        "desktopportraitwidth" => 300
    );

    protected $fonts = array(
        'font'      => array(
            'defaultName' => 'item-progressbar-font',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"right","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}'
        ),
        'fontlabel' => array(
            'defaultName' => 'item-progressbar-fontlabel',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'progressbar';
    }

    public function getTitle() {
        return n2_('Progress bar');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--progressbar';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemProgressBarFrontend($this, $id, $itemData, $layer);
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/progressbar.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function getValues() {

        return parent::getValues() + array(
                'value'             => 50,
                'startvalue'        => 0,
                'total'             => 100,
                'color'             => '00000080',
                'color2'            => '64c133ff',
                'pre'               => '',
                'post'              => '%',
                'label'             => 'Progress',
                'labelplacement'    => 'before',
                'animationduration' => 1000,
                'animationdelay'    => 0
            );
    }


    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('label', $slide->fill($data->get('label', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('fontlabel'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('fontlabel', $import->fixSection($data->get('fontlabel')));

        return $data;
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Font($row1, 'item-progressbar-font', n2_('Progress bar'), $this->fonts['font']['value'], array(
            'mode' => 'simple'
        ));

        new Font($row1, 'item-progressbar-fontlabel', n2_('Label'), $this->fonts['fontlabel']['value'], array(
            'mode' => 'simple'
        ));

    }

    public function renderFields($container) {
        $counter = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-progressbar-counter', n2_('Counter'));
        new Number($counter, 'value', n2_('Value'), '', array(
            'wide' => 5
        ));
        new Number($counter, 'startvalue', n2_('Start from'), '', array(
            'wide' => 5
        ));
        new Number($counter, 'total', n2_('Total'), '', array(
            'wide' => 5
        ));

        $display = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-progressbar-display', n2_('Display'));
        new Color($display, 'color', n2_('Color'), '', array(
            'alpha' => true
        ));
        new Color($display, 'color2', n2_('Active color'), '', array(
            'alpha' => true
        ));

        $labels = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-progressbar-labels', n2_('Labels'));
        new Text($labels, 'label', n2_('Label'), '', array(
            'style' => 'width:150px;'
        ));
        new Select($labels, 'labelplacement', n2_('Placement'), '', array(
            'options' => array(
                'before' => n2_('Before'),
                'over'   => n2_('Over'),
                'after'  => n2_('After')
            )
        ));
        new Text($labels, 'pre', n2_('Pre'), '', array(
            'style' => 'width:40px;'
        ));
        new Text($labels, 'post', n2_('Post'), '', array(
            'style' => 'width:40px;'
        ));

        $animation = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-progressbar-animation', n2_('Animation'));
        new Number($animation, 'animationduration', n2_('Animation duration'), 1, array(
            'min'  => 0,
            'wide' => 5,
            'unit' => 'ms'
        ));
        new Number($animation, 'animationdelay', n2_('Delay'), 0, array(
            'min'  => 0,
            'wide' => 5,
            'unit' => 'ms'
        ));

        new HiddenFont($counter, 'font', n2_('Font') . ' - ' . n2_('Counter'), '', array(
            'mode' => 'simple'
        ));

        new HiddenFont($counter, 'fontlabel', n2_('Font') . ' - ' . n2_('Label'), '', array(
            'mode' => 'simple'
        ));
    }
}