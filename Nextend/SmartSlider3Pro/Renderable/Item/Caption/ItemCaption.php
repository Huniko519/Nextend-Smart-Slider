<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Caption;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\Hidden\HiddenFont;
use Nextend\Framework\Form\Element\Mixed;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\LinkTarget;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Url;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemCaption extends AbstractItem {

    protected $ordering = 4;

    protected $layerProperties = array(
        "desktopportraitleft"  => 0,
        "desktopportraittop"   => 0,
        "desktopportraitwidth" => 200
    );

    protected $fonts = array(
        'fonttitle' => array(
            'defaultName' => 'item-caption-font-title',
            'value'       => '{"data":[{"color":"ffffffff","size":"14||px","align":"inherit"}]}'
        ),
        'font'      => array(
            'defaultName' => 'item-caption-font',
            'value'       => '{"data":[{"color":"ffffffff","size":"14||px","align":"inherit"}]}'
        )
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'caption';
    }

    public function getTitle() {
        return n2_('Caption');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--imagecaption';
    }

    public function getGroup() {
        return n2_x('Special', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemCaptionFrontend($this, $id, $itemData, $layer);
    }

    public function globalDefaultItemFontAndStyle($container) {

        $table = new ContainerTable($container, $this->getType(), $this->getTitle());
        $row1  = $table->createRow($this->getType() . '-1');

        new Font($row1, 'item-caption-font-title', n2_('Title'), $this->fonts['fonttitle']['value'], array(
            'mode' => 'paragraph'
        ));

        new Font($row1, 'item-caption-font', n2_('Description'), $this->fonts['font']['value'], array(
            'mode' => 'paragraph'
        ));
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/caption.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function getValues() {

        return parent::getValues() + array(
                'animation'      => 'Simple|*|left|*|0',
                'image'          => '$ss3-frontend$/images/placeholder/image.png',
                'alt'            => '',
                'href'           => '#',
                'href-target'    => '_self',
                'href-rel'       => '',
                'verticalalign'  => 'center',
                'content'        => n2_('Caption'),
                'description'    => '',
                'color'          => '00000080',
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
        $data->set('alt', $slide->fill($data->get('alt', '')));
        $data->set('content', $slide->fill($data->get('content', '')));
        $data->set('description', $slide->fill($data->get('description', '')));
        $data->set('href', $slide->fill($data->get('href', '#|*|')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('image'));
        $export->addVisual($data->get('font'));
        $export->addVisual($data->get('fonttitle'));
        $export->addLightbox($data->get('href'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('image', $import->fixImage($data->get('image')));
        $data->set('font', $import->fixSection($data->get('font')));
        $data->set('fonttitle', $import->fixSection($data->get('fonttitle')));
        $data->set('href', $import->fixLightbox($data->get('href')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('image', ResourceTranslator::toUrl($data->get('image')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption', n2_('General'));

        new FieldImage($settings, 'image', n2_('Image'), '', array(
            'relatedAlt' => 'item_captionalt',
            'width'      => 220
        ));

        new Text($settings, 'content', n2_('Title'), '', array(
            'style' => 'width:302px;'
        ));
        new HiddenFont($settings, 'fonttitle', n2_('Font') . ' - ' . n2_('Title'), '', array(
            'mode' => 'paragraph'
        ));

        new Textarea($settings, 'description', n2_('Description'), '', array(
            'width' => 314
        ));
        new HiddenFont($settings, 'font', n2_('Font') . ' - ' . n2_('Description'), '', array(
            'mode' => 'paragraph'
        ));

        $link = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption-link', n2_('Link'));
        new Url($link, 'href', n2_('Link'), '', array(
            'relatedFields' => array(
                'item_captionhref-target',
                'item_captionhref-rel'
            ),
            'width'         => 248
        ));
        new LinkTarget($link, 'href-target', n2_('Target window'));
        new Text($link, 'href-rel', n2_('Rel'), '', array(
            'style'          => 'width:195px;',
            'tipLabel'       => n2_('Rel'),
            'tipDescription' => sprintf(n2_('Enter the %1$s rel attribute %2$s that represents the relationship between the current document and the linked document. Multiple rel attributes can be separated with space. E.g. nofollow noopener noreferrer'), '<a href="https://www.w3schools.com/TAGS/att_a_rel.asp" target="_blank">', '</a>')
        ));

        $animationFieldset = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption-animation', n2_('Animation'));
        $animation         = new Mixed($animationFieldset, 'animation', false, 'Simple|*|left|*|0');
        new Select($animation, 'animation-1', n2_('Animation'), '', array(
            'options'            => array(
                'Full'   => n2_('Full'),
                'Simple' => n2_('Simple'),
                'Fade'   => n2_('Fade')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'Full',
                        'Simple'
                    ),
                    'field'  => array(
                        'animationitem_captionanimation-2'
                    )
                )
            )
        ));
        new Select($animation, 'animation-2', n2_('Direction'), '', array(
            'options' => array(
                'top'    => n2_('Top'),
                'right'  => n2_('Right'),
                'bottom' => n2_('Bottom'),
                'left'   => n2_('Left')
            )
        ));
        new OnOff($animation, 'animation-3', n2_('Scale'), 0, array(
            'tipLabel'       => n2_('Scale'),
            'tipDescription' => n2_('Scales up the image on hover')
        ));

        $overlay = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption-overlay', n2_('Display'));
        new Color($overlay, 'color', n2_('Overlay background'), '00000080', array(
            'alpha' => true
        ));
        new Select($overlay, 'verticalalign', n2_('Vertical align'), '', array(
            'options'        => array(
                'flex-start' => n2_('Top'),
                'center'     => n2_('Center'),
                'flex-end'   => n2_('Bottom')
            ),
            'tipLabel'       => n2_('Vertical align'),
            'tipDescription' => n2_('Positions the text inside the overlay.')
        ));

        $seo = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption-seo', n2_('SEO'));
        new Text($seo, 'alt', 'SEO - ' . n2_('Alt tag'), '', array(
            'style' => 'width:302px;'
        ));

        $optimize = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-caption-optimize', n2_('Optimize'));
        new OnOff($optimize, 'image-optimize', n2_('Optimize image'), 1, array(
            'tipLabel'       => n2_('Optimize image'),
            'tipDescription' => n2_('You can turn off the Layer image optimization for this image, to resize it for tablet and mobile.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1839-caption-layer#optimize'
        ));

    }
}