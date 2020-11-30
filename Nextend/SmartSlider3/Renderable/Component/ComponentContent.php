<?php


namespace Nextend\SmartSlider3\Renderable\Component;


use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\Model\ModelSlides;
use Nextend\SmartSlider3\BackupSlider\ExportSlider;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\ComponentContainer;

class ComponentContent extends AbstractComponent {

    protected $type = 'content';

    protected $name = 'Content';

    protected $colAttributes = array(
        'class' => 'n2-ss-section-main-content n2-ss-layer-content n2-ow',
        'style' => ''
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

    public function getPlacement() {
        return 'default';
    }

    public function __construct($index, $owner, $group, $data) {
        parent::__construct($index, $owner, $group, $data);
        $this->container = new ComponentContainer($owner, $this, $data['layers']);
        $this->data->un_set('layers');

        $this->attributes['style'] = '';

        $innerAlign = $this->data->get('desktopportraitinneralign', 'inherit');
        if (!empty($innerAlign)) {
            $this->attributes['data-csstextalign'] = $innerAlign;
        }

        $this->colAttributes['data-verticalalign'] = $this->data->get('verticalalign', 'center');

        $this->colAttributes['style'] .= 'padding:' . $this->spacingToEm($this->data->get('desktopportraitpadding', '10|*|10|*|10|*|10|*|px+')) . ';';

        $this->renderBackground();

        $maxWidth = intval($this->data->get('desktopportraitmaxwidth', 0));
        if ($maxWidth > 0) {
            $this->attributes['style'] .= 'max-width: ' . $maxWidth . 'px;';

            $this->attributes['data-has-maxwidth'] = '1';
        } else {
            $this->attributes['data-has-maxwidth'] = '0';
        }
        $this->createDeviceProperty('maxwidth', '0');

        $this->attributes['data-cssselfalign'] = $this->data->get('desktopportraitselfalign', 'center');

        $this->createDeviceProperty('selfalign', 'center');


        $this->placement->attributes($this->attributes);

        if ($this->data->has('verticalalign')) {
            /**
             * Upgrade data to device specific
             */
            $this->data->set('desktopportraitverticalalign', $this->data->get('verticalalign'));
            $this->data->un_set('verticalalign');
        }
        $this->createDeviceProperty('verticalalign', 'center');

        $this->createDeviceProperty('padding', '10|*|10|*|10|*|10|*|px+');
        $this->createDeviceProperty('inneralign', 'inherit');

    }

    protected function pluginFontSize() {
        $this->attributes['data-adaptivefont'] = $this->data->get('adaptivefont', 1);

        $this->createDeviceProperty('fontsize', 100);
    }

    public function updateRowSpecificProperties($gutter, $width, $isLast) {
        $this->attributes['style'] .= 'width: ' . $width . '%;';

        if (!$isLast) {
            $this->attributes['style'] .= 'margin-right: ' . $gutter . 'px;margin-bottom: ' . $gutter . 'px;';
        }

    }

    public function render($isAdmin) {
        if ($this->isRenderAllowed()) {
            if ($isAdmin || $this->hasBackground || count($this->container->getLayers())) {

                $this->serveLocalStyle();
                if ($isAdmin) {
                    $this->admin();
                }

                $this->prepareHTML();

                $this->attributes['data-hasbackground'] = $this->hasBackground ? '1' : '0';

                $html = Html::tag('div', $this->colAttributes, parent::renderContainer($isAdmin));
                $html = $this->renderPlugins($html);

                return Html::tag('div', $this->attributes, $html);
            }
        }

        return '';
    }

    protected function addUniqueClass($class) {
        $this->attributes['class']    .= ' ' . $class;
        $this->colAttributes['class'] .= ' ' . $class . '-inner';
    }

    protected function admin() {

        $this->createProperty('bgimage', '');
        $this->createProperty('bgimagex', 50);
        $this->createProperty('bgimagey', 50);

        $this->createColorProperty('bgcolor', true, '00000000');
        $this->createProperty('bgcolorgradient', 'off');
        $this->createColorProperty('bgcolorgradientend', true, '00000000');
        $this->createColorProperty('bgcolor-hover', true);
        $this->createProperty('bgcolorgradient-hover');
        $this->createColorProperty('bgcolorgradientend-hover', true);

        $this->createProperty('opened', 1);


        $this->createProperty('id', '');
        $this->createProperty('uniqueclass', '');
        $this->createProperty('class', '');
        $this->createProperty('status');
        $this->createProperty('generatorvisible', '');

        $this->placement->adminAttributes($this->attributes);
    }


    /**
     * @param ExportSlider $export
     * @param array        $layer
     */
    public static function prepareExport($export, $layer) {
        if (!empty($layer['bgimage'])) {
            $export->addImage($layer['bgimage']);
        }

        $export->prepareLayer($layer['layers']);
    }

    public static function prepareImport($import, &$layer) {
        if (!empty($layer['bgimage'])) {
            $layer['bgimage'] = $import->fixImage($layer['bgimage']);
        }

        $import->prepareLayers($layer['layers']);
    }

    public static function prepareSample(&$layer) {
        if (!empty($layer['bgimage'])) {
            $layer['bgimage'] = ResourceTranslator::toUrl($layer['bgimage']);
        }

        ModelSlides::prepareSample($layer['layers']);
    }

    /**
     * @param AbstractRenderableOwner $slide
     * @param array                   $layer
     */
    public static function getFilled($slide, &$layer) {
        AbstractComponent::getFilled($slide, $layer);

        if (!empty($layer['bgimage'])) {
            $layer['bgimage'] = $slide->fill($layer['bgimage']);
        }

        $slide->fillLayers($layer['layers']);
    }
}