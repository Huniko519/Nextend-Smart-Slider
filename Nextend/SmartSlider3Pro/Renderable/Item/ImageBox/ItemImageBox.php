<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\ImageBox;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Hidden\HiddenStyle;
use Nextend\Framework\Form\Element\Icon;
use Nextend\Framework\Form\Element\IconTab;
use Nextend\Framework\Form\Element\MarginPadding;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\RichTextarea;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Form\Element\Radio\InnerAlign;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemImageBox extends AbstractItem {

    protected $ordering = 1;

    protected $layerProperties = array("desktopportraitwidth" => "300");

    protected $fonts = array(
        'fonttitle'       => array(
            'defaultName' => 'item-imagebox-fonttitle',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"32||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}'
        ),
        'fontdescription' => array(
            'defaultName' => 'item-imagebox-fontdescription',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"16||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"2","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"}]}'
        )
    );

    protected $styles = array(
        'style' => array(
            'defaultName' => 'item-imagebox-style',
            'value'       => ''
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'imagebox';
    }

    public function getTitle() {
        return n2_('Image box');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--imagebox';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemImageBoxFrontend($this, $id, $itemData, $layer);
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/imagebox.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function getValues() {

        return parent::getValues() + array(
                'layout'          => 'top',
                'padding'         => '10|*|10|*|10|*|10',
                'inneralign'      => 'center',
                'verticalalign'   => 'flex-start',
                'image'           => '$ss3-frontend$/images/placeholder/image.png',
                'imagewidth'      => 100,
                'alt'             => '',
                'icon'            => '',
                'iconsize'        => 64,
                'iconcolor'       => 'ffffffff',
                'heading'         => n2_('Heading'),
                'headingpriority' => 'div',
                'description'     => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'href'            => '#',
                'href-target'     => '_self',
                'href-rel'        => '',
                'fullwidth'       => 0,
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

        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('description', $slide->fill($data->get('description', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));
        $data->set('image', $slide->fill($data->get('image', '')));
        $data->set('alt', $slide->fill($data->get('alt', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addVisual($data->get('fonttitle'));
        $export->addVisual($data->get('fontdescription'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('fonttitle', $import->fixSection($data->get('fonttitle')));
        $data->set('fontdescription', $import->fixSection($data->get('fontdescription')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', ResourceTranslator::toUrl($data->get('image')));

        return $data;
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Font($row1, 'item-imagebox-fonttitle', n2_('Title'), $this->fonts['fonttitle']['value'], array(
            'mode' => 'hover'
        ));

        new Font($row1, 'item-imagebox-fontdescription', n2_('Description'), $this->fonts['fontdescription']['value'], array(
            'mode' => 'paragraph'
        ));

        new Style($row1, 'item-imagebox-style', n2_('Image box'), $this->styles['style']['value'], array(
            'mode' => 'heading'
        ));
    }

    public function renderFields($container) {
        $imageSettings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'image-settings', n2_('General'));

        new IconTab($imageSettings, 'imagetype', n2_('Type'), 'image', array(
            'options'            => array(
                'image' => 'ssi_16 ssi_16--image',
                'icon'  => 'ssi_16 ssi_16--star'
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'image'
                    ),
                    'field'  => array(
                        'item_imageboximage',
                        'item_imageboximagewidth',
                        'item_imageboxalt'
                    )
                ),
                array(
                    'values' => array(
                        'icon'
                    ),
                    'field'  => array(
                        'item_imageboxicon',
                        'item_imageboxiconsize',
                        'item_imageboxiconcolor',
                    )
                )
            ),
            'tooltips'           => array(
                'image' => n2_('Image'),
                'icon'  => n2_('Icon'),
            )
        ));

        new FieldImage($imageSettings, 'image', n2_('Image'), '', array(
            'relatedAlt' => 'item_imageboxalt',
            'width'      => 140
        ));

        new NumberSlider($imageSettings, 'imagewidth', n2_('Width'), '', array(
            'max'  => 100,
            'unit' => '%',
            'wide' => 3
        ));
        new Text($imageSettings, 'alt', 'SEO - ' . n2_('Alt tag'), '', array(
            'style' => 'width:218px;'
        ));


        new Icon($imageSettings, 'icon', n2_('Icon'), '', array(
            'hasClear' => true
        ));
        new NumberSlider($imageSettings, 'iconsize', n2_('Size'), 100, array(
            'min'       => 8,
            'max'       => 400,
            'sliderMax' => 200,
            'step'      => 4,
            'wide'      => 3,
            'unit'      => 'px'
        ));
        new Color($imageSettings, 'iconcolor', n2_('Color'), '', array(
            'alpha' => true
        ));

        new OnOff($imageSettings, 'fullwidth', n2_('Full width'), 1);

        $text = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'text-settings', n2_('Text'));
        new Text($text, 'heading', n2_('Heading'), n2_('Heading'), array(
            'style' => 'width:226px;'
        ));
        new Select($text, 'headingpriority', n2_('Tag'), 'div', array(
            'options' => array(
                'div' => 'div',
                '1'   => 'H1',
                '2'   => 'H2',
                '3'   => 'H3',
                '4'   => 'H4',
                '5'   => 'H5',
                '6'   => 'H6'
            )
        ));

        new HiddenFont($text, 'fonttitle', n2_('Heading'), '', array(
            'mode' => 'hover'
        ));

        new RichTextarea($text, 'description', n2_('Description'), '', array(
            'fieldStyle' => 'height: 120px; width: 314px;resize: vertical;'
        ));

        new HiddenFont($text, 'fontdescription', n2_('Description'), '', array(
            'mode' => 'paragraph'
        ));

        new HiddenStyle($text, 'style', false, '', array(
            'mode' => 'box'
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-imagebox-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'relatedFields' => array(
                'item_imageboxhref-target',
                'item_imageboxhref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-imagebox', n2_('Display'));

        new Select($settings, 'layout', n2_('Layout'), '', array(
            'options'            => array(
                'top'    => n2_('Top'),
                'left'   => n2_('Left'),
                'right'  => n2_('Right'),
                'bottom' => n2_('Bottom')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'left',
                        'right'
                    ),
                    'field'  => array(
                        'item_imageboxverticalalign'
                    )
                )
            )
        ));

        $padding = new MarginPadding($settings, 'padding', n2_('Padding'), '10|*|10|*|10|*|10', array(
            'unit' => 'px'
        ));
        for ($i = 1; $i < 5; $i++) {
            new NumberAutoComplete($padding, 'padding-' . $i, false, '', array(
                'values' => array(
                    0,
                    5,
                    10,
                    20,
                    30
                ),
                'wide'   => 3
            ));
        }

        new InnerAlign($settings, 'inneralign', n2_('Inner align'));
        new Select($settings, 'verticalalign', n2_('Vertical align'), '', array(
            'options'        => array(
                'flex-start' => n2_('Top'),
                'center'     => n2_('Center'),
                'flex-end'   => n2_('Bottom')
            ),
            'tipLabel'       => n2_('Vertical align'),
            'tipDescription' => n2_('Positions the text inside the layer. Only works with left and right layout.')
        ));
    }
}