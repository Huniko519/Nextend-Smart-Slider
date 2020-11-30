<?php


namespace Nextend\SmartSlider3Pro\Widget\Group;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Pattern\PluggableTrait;
use Nextend\SmartSlider3\Form\Element\ControlTypePicker;
use Nextend\SmartSlider3\Widget\Group\AbstractWidgetGroup;

class Indicator extends AbstractWidgetGroup {

    use PluggableTrait;

    public $ordering = 4;

    public function __construct() {
        parent::__construct();

        $this->makePluggable('SliderWidgetIndicator');
    }

    public function getName() {
        return 'indicator';
    }

    public function getLabel() {
        return n2_('Indicator');
    }

    public function renderFields($container) {

        $form = $container->getForm();

        $this->compatibility($form);

        $table = new ContainerTable($container, 'widget-indicator', n2_('Indicator'));

        new OnOff($table->getFieldsetLabel(), 'widget-indicator-enabled', false, 0, array(
            'relatedFieldsOn' => array(
                'table-rows-widget-indicator'
            )
        ));

        $row1 = $table->createRow('widget-indicator-1');

        $url = $form->createAjaxUrl(array("slider/renderwidgetindicator"));
        new ControlTypePicker($row1, 'widgetindicator', $table, $url, $this);


        $row2 = $table->createRow('widget-indicator-2');

        new OnOff($row2, 'widget-indicator-display-hover', n2_('Shows on hover'), 0);

        $this->addHideOnFeature('widget-indicator-display-', $row2);

        new Text($row2, 'widget-indicator-exclude-slides', n2_('Hide on slides'), '', array(
            'tipLabel'       => n2_('Hide on slides'),
            'tipDescription' => n2_('List the slides separated by commas on which you want the controls to be hidden.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1807-slider-settings-autoplay#hide-on-slides-35'
        ));
    }
}