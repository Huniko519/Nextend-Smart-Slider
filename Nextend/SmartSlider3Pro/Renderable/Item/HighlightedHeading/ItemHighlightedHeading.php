<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\HighlightedHeading;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Hidden\HiddenStyle;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemHighlightedHeading extends AbstractItem {

    protected $ordering = 3;

    protected $fonts = array(
        'font' => array(
            'defaultName' => 'item-highlighted-heading-font',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"36||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{},{}]}'
        )
    );

    protected $styles = array(
        'style' => array(
            'defaultName' => 'item-highlighted-heading-style',
            'value'       => '{"data":[{},{"padding":"0|*|0|*|0|*|0|*|px"},{"padding":"0|*|0|*|0|*|0|*|px"}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'highlightedHeading';
    }

    public function getTitle() {
        return n2_('Highlighted heading');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--highlightheading';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemHighlightedHeadingFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {

        return parent::getValues() + array(
                'type'  => 'circle1',
                'color' => '5CBA3CFF',
                'width' => 10,
                'front' => 0,

                'before-text'      => 'This page is',
                'highlighted-text' => 'Amazing',
                'after-text'       => '',

                'animate'    => 1,
                'delay'      => 0,
                'duration'   => 1500,
                'loop'       => 0,
                'loop-delay' => 2000,

                'href'        => '#',
                'href-target' => '_self',
                'href-rel'    => '',

                'priority' => 'div',

                'class' => ''
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

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/highlightedHeading.n2less", array(
            "sliderid" => $renderable->elementId
        ));

        $renderable->addScript('N2Classes.ItemHighlightedHeading.svg=' . json_encode($this->getTypes()) . ';');
    }

    private function getTypes() {
        static $types = null;
        if ($types === null) {
            $types     = array();
            $extension = 'svg';
            $folder    = self::getAssetsPath() . '/svg/';
            $files     = Filesystem::files($folder);
            for ($i = 0; $i < count($files); $i++) {
                $pathInfo = pathinfo($files[$i]);
                if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {
                    $types[$pathInfo['filename']] = file_get_contents($folder . $files[$i]);
                }
            }
        }

        return $types;
    }

    private function getTypeOptions() {
        return array(
            ''                  => n2_('None'),
            'circle1'           => n2_('Circle 1'),
            'circle2'           => n2_('Circle 2'),
            'circle3'           => n2_('Circle 3'),
            'curly1'            => n2_('Curly 1'),
            'curly2'            => n2_('Curly 2'),
            'highlight1'        => n2_('Highlight 1'),
            'highlight2'        => n2_('Highlight 2'),
            'highlight3'        => n2_('Highlight 3'),
            'line_through1'     => n2_('Line Through 1'),
            'line_through2'     => n2_('Line Through 2'),
            'line_through3'     => n2_('Line Through 3'),
            'rectangle1'        => n2_('Rectangle 1'),
            'rectangle2'        => n2_('Rectangle 2'),
            'underline1'        => n2_('Underline 1'),
            'underline2'        => n2_('Underline 2'),
            'underline3'        => n2_('Underline 3'),
            'underline_double1' => n2_('Underline double 1'),
            'underline_double2' => n2_('Underline double 2'),
            'zigzag1'           => n2_('ZigZag 1'),
            'zigzag2'           => n2_('ZigZag 2'),
            'zigzag3'           => n2_('ZigZag 3'),
        );
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('heading', $slide->fill($data->get('heading', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Font($row1, 'item-highlighted-heading-font', false, $this->fonts['font']['value'], array(
            'mode' => 'hover'
        ));

        new Style($row1, 'item-highlighted-heading-style', false, $this->styles['style']['value'], array(
            'mode' => 'heading'
        ));
    }

    public function renderFields($container) {
        $text = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-highlighted-heading-text', n2_('General'));

        new Text($text, 'before-text', n2_('Before text'), '', array(
            'style' => 'width: 302px;'
        ));

        new Text($text, 'highlighted-text', n2_('Highlighted text'), '', array(
            'style' => 'width: 302px;'
        ));

        new Text($text, 'after-text', n2_('After text'), '', array(
            'style' => 'width: 302px;'
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-highlightheading-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'style'         => 'width:236px;',
            'relatedFields' => array(
                'item_highlightedHeadinghref-target',
                'item_highlightedHeadinghref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-highlighted-heading', n2_('Highlight'));

        new Select($settings, 'type', n2_('Type'), '', array(
            'options'       => $this->getTypeOptions(),
            'relatedFields' => array(
                'item_highlightedHeadingcolor',
                'item_highlightedHeadingwidth',
                'item_highlightedHeadingfront',
                'fieldset-layer-window-item-highlightheading-animation'
            )
        ));
        new Color($settings, 'color', n2_('Color'), '', array(
            'alpha' => true
        ));
        new NumberSlider($settings, 'width', n2_('Width'), '', array(
            'max'  => 100,
            'min'  => 1,
            'unit' => 'px',
            'wide' => 3
        ));
        new OnOff($settings, 'front', n2_('Bring front'), 0, array(
            'tipLabel'       => n2_('Bring front'),
            'tipDescription' => n2_('Puts the shape on top of the text.')
        ));

        $animation = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-highlightheading-animation', n2_('Animation'));
        new OnOff($animation, 'animate', n2_('Animate'), 1, array(
            'relatedFieldsOn' => array(
                'item_highlightedHeadingdelay',
                'item_highlightedHeadingduration',
                'item_highlightedHeadingloop-group'
            )
        ));
        new Number($animation, 'delay', n2_('Delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5
        ));
        new Number($animation, 'duration', n2_('Duration'), 1500, array(
            'unit' => 'ms',
            'wide' => 5,
            'post' => 'break'
        ));

        $groupingLoop = new Grouping($animation, 'loop-group');

        new OnOff($groupingLoop, 'loop', n2_x('Loop', 'Effect'), 1, array(
            'relatedFieldsOn' => array(
                'item_highlightedHeadingloop-delay'
            )
        ));
        new Number($groupingLoop, 'loop-delay', n2_('Loop delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5
        ));

        $dev = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-highlightheading-dev', n2_('Advanced'));
        new Select($dev, 'priority', 'Tag', 'div', array(
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

        new HiddenFont($settings, 'font', n2_('Font') . ' - ' . n2_('Heading'), '', array(
            'mode' => 'highlight'
        ));

        new HiddenStyle($settings, 'style', n2_('Style') . ' - ' . n2_('Heading'), '', array(
            'mode' => 'highlight'
        ));


        new Text($dev, 'class', n2_('CSS Class'), '', array(
            'style'          => 'width:226px;',
            'tipLabel'       => n2_('CSS Class'),
            'tipDescription' => n2_('Class on the selected tag element.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1812-layer-style#advanced'
        ));

    }
}