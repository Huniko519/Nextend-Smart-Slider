<?php


namespace Nextend\SmartSlider3\Widget\Thumbnail\Basic;


use Nextend\Framework\Form\Element\Font;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Style;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Form\Element\Group\WidgetPosition;
use Nextend\SmartSlider3\Widget\AbstractWidget;

class ThumbnailBasic extends AbstractWidget {

    protected $key = 'widget-thumbnail-';

    protected $defaults = array(
        'widget-thumbnail-minimum-thumbnail-count' => 2,
        'widget-thumbnail-position-mode'           => 'simple',
        'widget-thumbnail-position-area'           => 12,
        'widget-thumbnail-action'                  => 'click',
        'widget-thumbnail-style-bar'               => '{"data":[{"backgroundcolor":"242424ff","padding":"3|*|3|*|3|*|3|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":""}]}',
        'widget-thumbnail-style-slides'            => '{"data":[{"backgroundcolor":"00000000","padding":"0|*|0|*|0|*|0|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|ffffff00","borderradius":"0","opacity":"40","extra":"margin: 3px;\ntransition: all 0.4s;\nbackground-size: cover;"},{"border":"0|*|solid|*|ffffffcc","opacity":"100","extra":""}]}',
        'widget-thumbnail-arrow'                   => 1,
        'widget-thumbnail-arrow-image'             => '',
        'widget-thumbnail-arrow-width'             => 26,
        'widget-thumbnail-arrow-offset'            => 0,
        'widget-thumbnail-arrow-prev-alt'          => 'previous arrow',
        'widget-thumbnail-arrow-next-alt'          => 'next arrow',
        'widget-thumbnail-title-style'             => '{"data":[{"backgroundcolor":"000000ab","padding":"3|*|10|*|3|*|10|*|px","boxshadow":"0|*|0|*|0|*|0|*|000000ff","border":"0|*|solid|*|000000ff","borderradius":"0","extra":"bottom: 0;\nleft: 0;"}]}',
        'widget-thumbnail-title'                   => 0,
        'widget-thumbnail-title-font'              => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000ab","afont":"Montserrat","lineheight":"1.2","bold":0,"italic":0,"underline":0,"align":"left"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
        'widget-thumbnail-description'             => 0,
        'widget-thumbnail-description-font'        => '{"data":[{"color":"ffffffff","size":"12||px","tshadow":"0|*|0|*|0|*|000000ab","afont":"Montserrat","lineheight":"1.3","bold":0,"italic":0,"underline":0,"align":"left"},{"color":"fc2828ff","afont":"google(@import url(http://fonts.googleapis.com/css?family=Raleway);),Arial","size":"25||px"},{}]}',
        'widget-thumbnail-caption-placement'       => 'overlay',
        'widget-thumbnail-caption-size'            => 100,
        'widget-thumbnail-group'                   => 1,
        'widget-thumbnail-orientation'             => 'auto',
        'widget-thumbnail-size'                    => '100%',
        'widget-thumbnail-show-image'              => 1,
        'widget-thumbnail-width'                   => 100,
        'widget-thumbnail-height'                  => 60,
        'widget-thumbnail-align-content'           => 'start',
        'widget-thumbnail-invert-group-direction'  => 0
    );


    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'widget-thumbnail-default-row-1');

        new WidgetPosition($row1, 'widget-thumbnail-position', n2_('Position'));
        new Select($row1, 'widget-thumbnail-action', n2_('Action'), '', array(
            'options' => array(
                'click'      => n2_('Click'),
                'mouseenter' => n2_('Hover')
            )
        ));
    

        new Select($row1, 'widget-thumbnail-align-content', n2_('Align thumbnails'), '', array(
            'options' => array(
                'start'         => n2_('Start'),
                'center'        => n2_('Center'),
                'end'           => n2_('End'),
                'space-between' => n2_('Space between'),
                'space-around'  => n2_('Space around')
            )
        ));

        new Style($row1, 'widget-thumbnail-style-bar', n2_('Bar'), '', array(
            'mode'    => 'simple',
            'style2'  => 'sliderwidget-thumbnail-style-slides',
            'preview' => 'SmartSliderAdminWidgetThumbnailBasic'
        ));

        new Style($row1, 'widget-thumbnail-style-slides', n2_('Thumbnail'), '', array(
            'mode'    => 'dot',
            'style2'  => 'sliderwidget-thumbnail-style-bar',
            'preview' => 'SmartSliderAdminWidgetThumbnailBasic'
        ));

        $rowCaption = new FieldsetRow($container, 'widget-thumbnail-default-row-caption');
        new Style($rowCaption, 'widget-thumbnail-title-style', n2_('Caption'), '', array(
            'mode'    => 'simple',
            'post'    => 'break',
            'font'    => 'sliderwidget-thumbnail-title-font',
            'preview' => 'SmartSliderAdminWidgetThumbnailBasic'
        ));

        new OnOff($rowCaption, 'widget-thumbnail-title', n2_('Title'), '', array(
            'relatedFieldsOn' => array(
                'sliderwidget-thumbnail-title-font'
            )
        ));
        new Font($rowCaption, 'widget-thumbnail-title-font', '', '', array(
            'mode'    => 'simple',
            'style'   => 'sliderwidget-thumbnail-title-style',
            'preview' => 'SmartSliderAdminWidgetThumbnailBasic'
        ));

        new OnOff($rowCaption, 'widget-thumbnail-description', n2_('Description'), '', array(
            'relatedFieldsOn' => array(
                'sliderwidget-thumbnail-description-font'
            )
        ));
        new Font($rowCaption, 'widget-thumbnail-description-font', '', '', array(
            'mode'    => 'simple',
            'style'   => 'sliderwidget-thumbnail-title-style',
            'preview' => 'SmartSliderAdminWidgetThumbnailBasic'
        ));


        new Select($rowCaption, 'widget-thumbnail-caption-placement', n2_('Placement'), '', array(
            'options' => array(
                'before'  => n2_('Before'),
                'overlay' => n2_('Overlay'),
                'after'   => n2_('After')
            )
        ));

        new Number($rowCaption, 'widget-thumbnail-caption-size', n2_('Size'), '', array(
            'wide'           => 5,
            'unit'           => 'px',
            'tipLabel'       => n2_('Size'),
            'tipDescription' => n2_('The height (horizontal orientation) or width (vertical orientation) of the caption container.')
        ));
        $rowArrow = new FieldsetRow($container, 'widget-thumbnail-default-row-arrow');

        new OnOff($rowArrow, 'widget-thumbnail-arrow', n2_('Arrow'), '', array(
            'relatedFieldsOn' => array(
                'sliderwidget-thumbnail-arrow-image',
                'sliderwidget-thumbnail-arrow-width',
                'sliderwidget-thumbnail-arrow-offset',
                'sliderwidget-thumbnail-arrow-prev-alt',
                'sliderwidget-thumbnail-arrow-next-alt'
            )
        ));
        new Number($rowArrow, 'widget-thumbnail-arrow-width', n2_('Size'), 26, array(
            'wide' => 4,
            'unit' => 'px'
        ));
        new Number($rowArrow, 'widget-thumbnail-arrow-offset', n2_('Offset'), 0, array(
            'wide' => 4,
            'unit' => 'px'
        ));
        new Text($rowArrow, 'widget-thumbnail-arrow-prev-alt', n2_('Previous alt tag'), 'previous arrow', array(
            'style' => 'width:100px;'
        ));
        new Text($rowArrow, 'widget-thumbnail-arrow-next-alt', n2_('Next alt tag'), 'next arrow', array(
            'style' => 'width:100px;'
        ));
        new FieldImage($rowArrow, 'widget-thumbnail-arrow-image', n2_('Next arrow image'), '', array(
            'tipLabel'       => n2_('Next arrow image'),
            'tipDescription' => n2_('The previous arrow image will be mirrored.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1857-thumbnails#arrow'
        ));

    

        $row4 = new FieldsetRow($container, 'widget-thumbnail-default-row-4');

        new Number($row4, 'widget-thumbnail-minimum-thumbnail-count', n2_('Minimum thumbnail count'), '', array(
            'unit'           => n2_x('slides', 'Unit'),
            'wide'           => 3,
            'tipLabel'       => n2_('Minimum thumbnail count'),
            'tipDescription' => n2_('If your thumbnail is vertical, it hides the thumbnails when the set number of thumbnails can\'t fit.') . '<br>' . n2_('If your thumbnail is horizontal, it starts resizing the thumbnails when the set number of thumbnails can\'t fit.')
        ));
        new Number($row4, 'widget-thumbnail-group', n2_('Group by'), '', array(
            'unit'           => n2_x('thumbnails', 'Unit'),
            'wide'           => 3,
            'tipLabel'       => n2_('Group by'),
            'tipDescription' => n2_('You can break your thumbnails into rows or columns.')
        ));

        new OnOff($row4, 'widget-thumbnail-invert-group-direction', n2_('Invert group direction'), '', array(
            'tipLabel'       => n2_('Invert group direction'),
            'tipDescription' => n2_('Makes the thumbnail order follow the set orientation.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1857-thumbnails#invert-group-direction'
        ));

        new Select($row4, 'widget-thumbnail-orientation', n2_('Orientation'), '', array(
            'options' => array(
                'auto'       => n2_('Auto'),
                'horizontal' => n2_('Horizontal'),
                'vertical'   => n2_('Vertical')
            )
        ));

        new Text($row4, 'widget-thumbnail-size', n2_('Size'), '', array(
            'style'          => 'width:150px;',
            'tipLabel'       => n2_('Size'),
            'tipDescription' => n2_('The height (horizontal orientation) or width (vertical orientation) of the thumbnail container in px or %.')
        ));

    
    }


    public function prepareExport($export, $params) {

        $export->addVisual($params->get($this->key . 'style-bar'));
        $export->addVisual($params->get($this->key . 'style-slides'));
        $export->addVisual($params->get($this->key . 'title-style'));

        $export->addVisual($params->get($this->key . 'title-font'));
        $export->addVisual($params->get($this->key . 'description-font'));
    }

    public function prepareImport($import, $params) {

        $params->set($this->key . 'style-bar', $import->fixSection($params->get($this->key . 'style-bar', '')));
        $params->set($this->key . 'style-slides', $import->fixSection($params->get($this->key . 'style-slides', '')));
        $params->set($this->key . 'title-style', $import->fixSection($params->get($this->key . 'title-style', '')));

        $params->set($this->key . 'title-font', $import->fixSection($params->get($this->key . 'title-font', '')));
        $params->set($this->key . 'description-font', $import->fixSection($params->get($this->key . 'description-font', '')));
    }
}