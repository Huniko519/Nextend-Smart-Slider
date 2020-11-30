<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\AnimatedHeading;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
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
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemAnimatedHeading extends AbstractItem {

    protected $ordering = 2;

    protected $fonts = array(
        'font' => array(
            'defaultName' => 'item-animated-heading-font',
            'value'       => '{"data":[{"extra":"","color":"ffffffff","size":"36||px","tshadow":"0|*|0|*|0|*|000000ff","lineheight":"1.5","bold":0,"italic":0,"underline":0,"align":"inherit","letterspacing":"normal","wordspacing":"normal","texttransform":"none"},{},{}]}'
        )
    );

    protected $styles = array(
        'style' => array(
            'defaultName' => 'item-animated-heading-style',
            'value'       => '{"data":[{},{"padding":"0|*|0|*|0|*|0|*|px"},{}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'animatedHeading';
    }

    public function getTitle() {
        return n2_('Animated heading');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--animatedheading';
    }

    public function getGroup() {
        return n2_('Special');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemAnimatedHeadingFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {

        return parent::getValues() + array(
                'type'          => 'slide',
                'color'         => 'ffffffff',
                'color2'        => '000000',
                'loop'          => 0,
                'delay'         => 0,
                'speed'         => 100,
                'show-duration' => 1500,
                'animate-width' => 1,

                'priority'      => 'div',
                'before-text'   => 'We are Passionate About',
                'animated-text' => "Amazing Food\nGreat Hospitality",
                'href'          => '#',
                'href-target'   => '_self',
                'href-rel'      => '',
                'after-text'    => '',
                'title'         => '',

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

        $renderable->addLess(self::getAssetsPath() . "/animatedHeading.n2less", array(
            "sliderid" => $renderable->elementId
        ));
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

        new Font($row1, 'item-animated-heading-font', false, $this->fonts['font']['value'], array(
            'mode' => 'hover'
        ));

        new Style($row1, 'item-animated-heading-style', false, $this->styles['style']['value'], array(
            'mode' => 'heading'
        ));
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-animated-heading-text', n2_('General'));

        new Text($settings, 'before-text', n2_('Before text'), '', array(
            'style' => 'width: 302px;'
        ));

        new Textarea($settings, 'animated-text', n2_('Animated text'), '', array(
            'height' => 70,
            'width'  => 314
        ));

        new Text($settings, 'after-text', n2_('After text'), '', array(
            'style' => 'width: 302px;'
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-animated-heading-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'style'         => 'width:236px;',
            'relatedFields' => array(
                'item_animatedHeadinghref-target',
                'item_animatedHeadinghref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $animation = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-animated-heading-animation', n2_('Animation'));

        new Select($animation, 'type', n2_('Type'), '', array(
            'options'            => array(
                'fade'        => n2_('Fade'),
                'rotating'    => n2_('Rotating'),
                'drop-in'     => n2_('Drop-in'),
                'slide'       => n2_x('Slide', 'Animation'),
                'slide-down'  => n2_('Slide down'),
                'typewriter1' => n2_('Typewriter 1'),
                'typewriter2' => n2_('Typewriter 2'),
                'chars'       => n2_('Chars'),
                'chars2'      => n2_('Chars 2')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'fade',
                        'rotating',
                        'drop-in',
                        'slide',
                        'slide-down',
                        'chars',
                        'chars2'
                    ),
                    'field'  => array(
                        'item_animatedHeadinganimate-width'
                    )
                ),
                array(
                    'values' => array(
                        'typewriter1',
                        'typewriter2'
                    ),
                    'field'  => array(
                        'item_animatedHeadingcolor'
                    )
                ),
                array(
                    'values' => array(
                        'typewriter2'
                    ),
                    'field'  => array(
                        'item_animatedHeadingcolor2'
                    )
                )
            )
        ));
        new OnOff($animation, 'animate-width', n2_('Auto width'), 1);
        new Color($animation, 'color', n2_('Cursor color'), '', array(
            'alpha' => true
        ));
        new Color($animation, 'color2', n2_('Text color'), '');

        new OnOff($animation, 'loop', n2_x('Loop', 'Effect'), 1);

        new Number($animation, 'delay', n2_('Delay'), 0, array(
            'unit' => 'ms',
            'wide' => 5,
            'min'  => 0
        ));
        new NumberSlider($animation, 'speed', n2_('Speed'), 100, array(
            'style'     => 'width:35px;',
            'unit'      => '%',
            'min'       => 10,
            'max'       => 400,
            'step'      => 1,
            'sliderMax' => 150
        ));
        new Number($animation, 'show-duration', n2_('Show duration'), 1500, array(
            'unit' => 'ms',
            'wide' => 5,
            'min'  => 0
        ));

        $dev = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-animated-heading-developer', n2_('Advanced'));
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

        new HiddenFont($settings, 'font', false, '', array(
            'mode' => 'highlight'
        ));

        new HiddenStyle($settings, 'style', false, '', array(
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