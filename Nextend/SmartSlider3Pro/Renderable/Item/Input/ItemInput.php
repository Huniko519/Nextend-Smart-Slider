<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Input;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Hidden\HiddenStyle;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemInput extends AbstractItem {

    protected $ordering = 100;

    protected $fonts = array(
        'inputfont'  => array(
            'defaultName' => 'item-input-font',
            'value'       => '{"data":[{"color":"000000ff","size":"15||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat,Arial","lineheight":"44px","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none","extra":"height:44px;"},{},{}]}'
        ),
        'buttonfont' => array(
            'defaultName' => 'item-input-button-font',
            'value'       => '{"data":[{"color":"ffffffff","size":"14||px","tshadow":"0|*|0|*|0|*|000000ff","afont":"Montserrat,Arial","lineheight":"44px","bold":0,"italic":0,"underline":0,"align":"left","letterspacing":"normal","wordspacing":"normal","texttransform":"none","extra":""},{},{}]}'
        )
    );

    protected $styles = array(
        'style'       => array(
            'defaultName' => 'item-input-container-style',
            'value'       => ''
        ),
        'inputstyle'  => array(
            'defaultName' => 'item-input-style',
            'value'       => '{"data":[{"backgroundcolor":"ffffffff","padding":"0|*|20|*|0|*|20|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""},{}]}'
        ),
        'buttonstyle' => array(
            'defaultName' => 'item-input-button-style',
            'value'       => '{"data":[{"backgroundcolor":"04bc8fff","padding":"0|*|35|*|0|*|35|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""},{}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'input';
    }

    public function getTitle() {
        return n2_('Input');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--input';
    }

    public function getGroup() {
        return n2_x('Advanced', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemInputFrontend($this, $id, $itemData, $layer);
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Style($row1, 'item-input-container-style', n2_('Container'), $this->styles['style']['value'], array(
            'mode' => 'heading'
        ));

        new Font($row1, 'item-input-font', n2_('Input'), $this->fonts['inputfont']['value'], array(
            'mode' => 'input'
        ));

        new Style($row1, 'item-input-style', n2_('Input'), $this->styles['inputstyle']['value'], array(
            'mode' => 'heading'
        ));

        new Font($row1, 'item-input-button-font', n2_('Button'), $this->fonts['buttonfont']['value'], array(
            'mode' => 'hover'
        ));

        new Style($row1, 'item-input-button-style', n2_('Button'), $this->styles['buttonstyle']['value'], array(
            'mode' => 'button'
        ));
    }


    public function getValues() {

        return parent::getValues() + array(
                'placeholder' => n2_('What are you looking for?'),
                'action'      => 'https://www.google.com/search',
                'method'      => 'GET',
                'target'      => '_self',
                'parameters'  => 'ie=utf-8&oe=utf-8',
                'name'        => 'q',
                'buttonlabel' => n2_('Search'),
                'submit'      => '',
                'class'       => '',
                'onsubmit'    => '',
                'onkeyup'     => ''
            );
    }


    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('parameters', $slide->fill($data->get('parameters')));
        $data->set('buttonlabel', $slide->fill($data->get('buttonlabel')));
        $data->set('action', $slide->fill($data->get('action')));
        $data->set('name', $slide->fill($data->get('name')));
        $data->set('placeholder', $slide->fill($data->get('placeholder')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('style'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('style', $import->fixSection($data->get('style')));

        return $data;
    }

    public function renderFields($container) {

        $text = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-input', n2_('Text'));

        new Warning($text, 'item-input-notice', n2_('We only suggest using this layer if you are a developer, since the Input layer requires deep understanding how HTML form works.'));

        new Text($text, 'placeholder', n2_('Placeholder text'), n2_('What are you looking for?'), array(
            'style' => 'width:170px;'
        ));

        new Text($text, 'buttonlabel', n2_('Label'), n2_('Button'), array(
            'style' => 'width:96px;'
        ));

        $developer = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-input-developer', n2_('Form'));
        new Text($developer, 'name', n2_('Input name'), 'q', array(
            'style' => 'width:80px;'
        ));
        new Select($developer, 'method', n2_('Method'), 'GET', array(
            'options' => array(
                'GET'  => 'GET',
                'POST' => 'POST'
            )
        ));
        new LinkTarget($developer, 'target', n2_('Target window'));
        new Text($developer, 'action', n2_('Form action'), 'https://www.google.com/search', array(
            'style' => 'width:302px;'
        ));
        new Textarea($developer, 'parameters', n2_('Parameters'), 'ie=utf-8&oe=utf-8', array(
            'width' => 314
        ));
        new Select($developer, 'submit', n2_('Slide action to submit'), '', array(
            'options' => array(
                ''           => n2_('Off'),
                'click'      => n2_('Click'),
                'mouseenter' => n2_('Mouse enter'),
                'mouseleave' => n2_('Mouse leave')
            )
        ));

        $js = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-input-js', n2_('JavaScript'));
        new Text($js, 'onsubmit', 'OnSubmit', '', array(
            'style' => 'width:133px;'
        ));
        new Text($js, 'onkeyup', 'OnKeyUp', '', array(
            'style' => 'width:133px;'
        ));

        $html = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-input-html', n2_('Advanced'));
        new Text($html, 'class', n2_('CSS Class'), '', array(
            'style'          => 'width:302px;',
            'tipLabel'       => n2_('CSS Class'),
            'tipDescription' => sprintf(n2_('Class on the %s element.'), '<form>'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1812-layer-style#advanced'
        ));


        new HiddenFont($text, 'inputfont', n2_('Input'), '', array(
            'mode' => 'paragraph'
        ));

        new HiddenStyle($text, 'inputstyle', n2_('Input'), '', array(
            'mode' => 'heading'
        ));

        new HiddenStyle($text, 'style', n2_('Container'), '', array(
            'mode' => 'heading'
        ));

        new HiddenFont($text, 'buttonfont', n2_('Button'), '', array(
            'mode' => 'hover'
        ));

        new HiddenStyle($text, 'buttonstyle', n2_('Button'), '', array(
            'mode' => 'heading'
        ));
    }
}