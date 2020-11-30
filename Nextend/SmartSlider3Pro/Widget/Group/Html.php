<?php


namespace Nextend\SmartSlider3Pro\Widget\Group;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Pattern\PluggableTrait;
use Nextend\SmartSlider3\Form\Element\ControlTypePicker;
use Nextend\SmartSlider3\Widget\Group\AbstractWidgetGroup;

class Html extends AbstractWidgetGroup {

    use PluggableTrait;

    public $ordering = 10;

    protected $showOnMobileDefault = 1;

    public function __construct() {
        parent::__construct();

        $this->makePluggable('SliderWidgetHtml');
    }

    public function getName() {
        return 'html';
    }

    public function getLabel() {
        return 'HTML';
    }

    public function renderFields($container) {

        $form = $container->getForm();

        $this->compatibility($form);

        $table = new ContainerTable($container, 'widget-html', 'HTML');

        new OnOff($table->getFieldsetLabel(), 'widget-html-enabled', false, 0, array(
            'relatedFieldsOn' => array(
                'table-rows-widget-html'
            )
        ));


        $row1 = $table->createRow('widget-html-1');

        $url = $form->createAjaxUrl(array("slider/renderwidgethtml"));
        new ControlTypePicker($row1, 'widgethtml', $table, $url, $this, 'html');


        $row2 = $table->createRow('widget-html-2');
        new OnOff($row2, 'widget-html-display-hover', n2_('Shows on hover'), 0);

        $this->addHideOnFeature('widget-html-display-', $row2);

        new Text($row2, 'widget-html-exclude-slides', n2_('Hide on slides'), '', array(
            'tipLabel'       => n2_('Hide on slides'),
            'tipDescription' => n2_('List the slides separated by commas on which you want the controls to be hidden.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1860-html#hide-on-slides'
        ));
    }
}