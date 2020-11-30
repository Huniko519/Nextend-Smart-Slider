<?php


namespace Nextend\SmartSlider3\Renderable\Component;


use Nextend\Framework\Parser\Color;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\Parser\Link;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\Model\ModelSlides;
use Nextend\SmartSlider3\BackupSlider\ExportSlider;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\ComponentContainer;

class ComponentRow extends AbstractComponent {

    protected $type = 'row';

    protected $rowAttributes = array(
        'class' => 'n2-ss-layer-row ',
        'style' => ''
    );

    protected $rowAttributesInner = array(
        'class' => 'n2-ss-layer-row-inner '
    );

    protected $localStyle = array(
        array(
            "group"    => "normal",
            "selector" => '-inner',
            "css"      => array(
                'transition' => 'transition:all .3s;transition-property:border,background-image,background-color,border-radius,box-shadow;'
            )
        ),
        array(
            "group"    => "hover",
            "selector" => '-inner:HOVER',
            "css"      => array()
        ),
    );

    protected $html = '';

    public function __construct($index, $owner, $group, $data) {
        parent::__construct($index, $owner, $group, $data);
        $this->container = new ComponentContainer($owner, $this, $data['cols']);
        $this->data->un_set('cols');
        $this->data->un_set('inneralign');

        $columns = $this->getColumns();

        for ($i = 0; $i < count($columns); $i++) {
            $columns[$i]->updateRowSpecificProperties($this->data->get('desktopportraitgutter', 20));
        }

        $this->rowAttributes['style'] .= 'padding:' . $this->spacingToEm($this->data->get('desktopportraitpadding', '10|*|10|*|10|*|10|*|px+')) . ';';

        $this->renderBackground();


        $fullWidth = $this->data->get('fullwidth', 1);
        if (!$fullWidth) {
            $this->attributes['data-frontend-fullwidth'] = '0';
        } else {
            $this->attributes['data-frontend-fullwidth'] = '1';
        }

        $stretch = $this->data->get('stretch', 0);
        if ($stretch) {
            $this->attributes['class'] .= ' n2-ss-stretch-layer';
        }

        $borderWidth = $this->data->get('borderwidth', '1|*|1|*|1|*|1');
        $borderStyle = $this->data->get('borderstyle', 'none');
        $borderColor = $this->data->get('bordercolor', 'ffffffff');

        if ($borderStyle != 'none') {
            $this->addLocalStyle('normal', 'border', $this->getBorderCSS($borderWidth, $borderStyle, $borderColor));
        }

        $borderWidthHover = $this->data->get('borderwidth-hover');
        $borderStyleHover = $this->data->get('borderstyle-hover');
        $borderColorHover = $this->data->get('bordercolor-hover');
        $isHoverDifferent = false;
        if (!empty($borderWidthHover) || $borderWidthHover != $borderWidth) {
            $isHoverDifferent = true;
        }
        if (!empty($borderStyleHover) || $borderStyleHover != $borderStyle) {
            $isHoverDifferent = true;
        }
        if (!empty($borderColorHover) || $borderColorHover != $borderColor) {
            $isHoverDifferent = true;
        }
        if ($isHoverDifferent) {
            if (empty($borderWidthHover)) $borderWidthHover = $borderWidth;
            if (empty($borderStyleHover)) $borderStyleHover = $borderStyle;
            if (empty($borderColorHover)) $borderColorHover = $borderColor;

            $this->addLocalStyle('hover', 'border', $this->getBorderCSS($borderWidthHover, $borderStyleHover, $borderColorHover));
        }

        $borderRadius = intval($this->data->get('borderradius', 0));
        $this->addLocalStyle('normal', 'borderradius', $this->getBorderRadiusCSS($borderRadius));

        $borderRadiusHover = intval($this->data->get('borderradius-hover'));
        if (!empty($borderRadiusHover) && $borderRadiusHover != $borderRadius) {
            $this->addLocalStyle('hover', 'borderradius', $this->getBorderRadiusCSS($borderRadiusHover));
        }

        $boxShadow = $this->data->get('boxshadow', '0|*|0|*|0|*|0|*|00000080');
        $this->addLocalStyle('normal', 'boxshadow', $this->getBoxShadowCSS($boxShadow));

        $boxShadowHover = $this->data->get('boxshadow-hover');
        if (!empty($boxShadowHover) && $boxShadowHover != $boxShadow) {
            $this->addLocalStyle('hover', 'boxshadow', $this->getBoxShadowCSS($boxShadowHover));
        }

        $this->placement->attributes($this->attributes);
        $innerAlign = $this->data->get('desktopportraitinneralign', 'inherit');
        if (!empty($innerAlign)) {
            $this->attributes['data-csstextalign'] = $innerAlign;
        }

        $this->createDeviceProperty('padding', '10|*|10|*|10|*|10|*|px+');
        $this->createDeviceProperty('gutter', 20);
        $this->createDeviceProperty('wrapafter', 0);
        $this->createDeviceProperty('inneralign', 'inherit');


        if (!AbstractComponent::$isAdmin) {
            $this->makeLink();
        }
    }

    public function render($isAdmin) {
        if ($this->isRenderAllowed()) {

            $this->serveLocalStyle();
            if ($isAdmin) {
                $this->admin();
            }
            $this->prepareHTML();

            $html = Html::tag('div', $this->rowAttributes, Html::tag('div', $this->rowAttributesInner, parent::renderContainer($isAdmin)));
            $html = $this->renderPlugins($html);

            return Html::tag('div', $this->attributes, $html);
        }

        return '';
    }

    /**
     * @return ComponentCol[]
     */
    protected function getColumns() {
        $layers  = $this->container->getLayers();
        $columns = array();
        for ($i = 0; $i < count($layers); $i++) {
            if ($layers[$i] instanceof ComponentCol) {
                $columns[] = $layers[$i];
            }
        }

        return $columns;
    }

    protected function addUniqueClass($class) {
        $this->attributes['class']    .= ' ' . $class;
        $this->rowAttributes['class'] .= ' ' . $class . '-inner';
    }

    private function makeLink() {

        $linkV1 = $this->data->get('link', '');
        if (!empty($linkV1)) {
            list($link, $target) = array_pad((array)Common::parse($linkV1), 2, '');
            $this->data->un_set('link');
            $this->data->set('href', $link);
            $this->data->set('href-target', $target);
        }

        $link = $this->data->get('href');

        if (($link != '#' && !empty($link))) {
            $target = $this->data->get('href-target');

            $link                          = Link::parse($this->owner->fill($link), $this->attributes);
            $this->attributes['data-href'] = $link;

            if (!isset($this->attributes['onclick']) && !isset($this->attributes['data-n2-lightbox'])) {
                if (!empty($target) && $target != '_self') {
                    $this->attributes['data-target'] = $target;
                }
                $this->attributes['onclick'] = "n2ss.openUrl(event);";
            }
            $this->attributes['style'] .= 'cursor:pointer;';

        }
    }

    protected function admin() {

        $linkV1 = $this->data->get('link', '');
        if (!empty($linkV1)) {
            list($link, $target) = array_pad((array)Common::parse($linkV1), 2, '');
            $this->data->un_set('link');
            $this->data->set('href', $link);
            $this->data->set('href-target', $target);
        }

        $this->createProperty('href', '');
        $this->createProperty('href-target', '_self');

        $this->createProperty('bgimage', '');
        $this->createProperty('bgimagex', 50);
        $this->createProperty('bgimagey', 50);

        $this->createColorProperty('bgcolor', true, '00000000');
        $this->createProperty('bgcolorgradient', 'off');
        $this->createColorProperty('bgcolorgradientend', true, '00000000');
        $this->createColorProperty('bgcolor-hover', true);
        $this->createProperty('bgcolorgradient-hover');
        $this->createColorProperty('bgcolorgradientend-hover', true);

        $this->createProperty('borderwidth', '1|*|1|*|1|*|1');
        $this->createProperty('borderstyle', 'none');
        $this->createProperty('bordercolor', 'FFFFFFFF');
        $this->createProperty('borderwidth-hover');
        $this->createProperty('borderstyle-hover');
        $this->createProperty('bordercolor-hover');

        $this->createProperty('borderradius', 0);
        $this->createProperty('borderradius-hover');

        $this->createProperty('boxshadow', '0|*|0|*|0|*|0|*|00000080');
        $this->createProperty('boxshadow-hover');

        $this->createProperty('fullwidth', '1');
        $this->createProperty('stretch', '0');

        $this->createProperty('opened', 1);

        parent::admin();
    }


    /**
     * @param ExportSlider $export
     * @param array        $layer
     */
    public static function prepareExport($export, $layer) {
        if (!empty($layer['bgimage'])) {
            $export->addImage($layer['bgimage']);
        }

        $export->prepareLayer($layer['cols']);
    }

    public static function prepareImport($import, &$layer) {
        if (!empty($layer['bgimage'])) {
            $layer['bgimage'] = $import->fixImage($layer['bgimage']);
        }

        $import->prepareLayers($layer['cols']);
    }

    public static function prepareSample(&$layer) {
        if (!empty($layer['bgimage'])) {
            $layer['bgimage'] = ResourceTranslator::toUrl($layer['bgimage']);
        }

        ModelSlides::prepareSample($layer['cols']);
    }

    /**
     * @param AbstractRenderableOwner $slide
     * @param array                   $layer
     */
    public static function getFilled($slide, &$layer) {
        AbstractComponent::getFilled($slide, $layer);

        $fields = array(
            'bgimage',
            'href'
        );

        foreach ($fields AS $field) {
            if (!empty($layer[$field])) {
                $layer[$field] = $slide->fill($layer[$field]);
            }
        }

        $slide->fillLayers($layer['cols']);
    }

    private function getBorderCSS($width, $style, $color) {
        if ($style != 'none') {

            $values    = explode('|*|', $width);
            $unit      = 'px';
            $values[4] = '';
            $css       = 'border-width:' . implode($unit . ' ', $values) . ';';

            $css .= 'border-style:' . $style . ';';
            $css .= 'border-color:' . Color::colorToRGBA($color) . ';';

            return $css;
        }

        return '';
    }

    private function getBorderRadiusCSS($borderRadius) {
        if ($borderRadius > 0) {
            return 'border-radius:' . $borderRadius . 'px;';
        }

        return '';
    }

    private function getBoxShadowCSS($boxShadow) {
        $boxShadowArray = explode('|*|', $boxShadow);
        if (count($boxShadowArray) == 5 && ($boxShadowArray[0] != 0 || $boxShadowArray[1] != 0 || $boxShadowArray[2] != 0 || $boxShadowArray[3] != 0) && Color::hex2alpha($boxShadowArray[4]) != 0) {
            return 'box-shadow:' . $boxShadowArray[0] . 'px ' . $boxShadowArray[1] . 'px ' . $boxShadowArray[2] . 'px ' . $boxShadowArray[3] . 'px ' . Color::colorToRGBA($boxShadowArray[4]) . ';';
        }

        return '';
    }
}