<?php


namespace Nextend\SmartSlider3Pro\Widget\Group;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Pattern\PluggableTrait;
use Nextend\SmartSlider3\Form\Element\ControlTypePicker;
use Nextend\SmartSlider3\Widget\Group\AbstractWidgetGroup;

class FullScreen extends AbstractWidgetGroup {

    use PluggableTrait;

    public $ordering = 9;

    public function __construct() {
        parent::__construct();

        $this->makePluggable('SliderWidgetFullScreen');
    }

    public function getName() {
        return 'fullscreen';
    }

    public function getLabel() {
        return n2_('Fullscreen');
    }

    public function renderFields($container) {

        $form = $container->getForm();

        $this->compatibility($form);

        /**
         * Used for field removal: /controls/widget-fullscreen
         */
        $table = new ContainerTable($container, 'widget-fullscreen', n2_('Fullscreen'));

        new OnOff($table->getFieldsetLabel(), 'widget-fullscreen-enabled', false, 0, array(
            'relatedFieldsOn' => array(
                'table-rows-widget-fullscreen'
            )
        ));

        $fullscreenWarning = $table->createRow('widget-fullscreen-warning');
        $warningText       = sprintf(n2_('%1$s %2$s does not support the full screen API %3$s so other elements of the page may overlap the slider in Fullscreen mode. To avoid such problems, we suggest hiding the Fullscreen control from mobile view!'), 'iPhone', '<a href="https://smartslider.helpscoutdocs.com/article/1859-fullscreen#safari" target="_blank">', '</a>');
        new Warning($fullscreenWarning, 'widget-fullscreen-warning-iphone', $warningText);

        $row1 = $table->createRow('widget-fullscreen-1');

        $url = $form->createAjaxUrl(array("slider/renderwidgetfullscreen"));
        new ControlTypePicker($row1, 'widgetfullscreen', $table, $url, $this, 'image');


        $row2 = $table->createRow('widget-fullscreen-2');

        new OnOff($row2, 'widget-fullscreen-display-hover', n2_('Shows on hover'), 0);

        $this->addHideOnFeature('widget-fullscreen-display-', $row2);

        new Text($row2, 'widget-fullscreen-exclude-slides', n2_('Hide on slides'), '', array(
            'tipLabel'       => n2_('Hide on slides'),
            'tipDescription' => n2_('List the slides separated by commas on which you want the controls to be hidden.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1859-fullscreen#hide-on-slides'
        ));
    }
}