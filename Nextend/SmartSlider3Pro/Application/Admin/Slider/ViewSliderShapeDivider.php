<?php

namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Tab;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Form;
use Nextend\Framework\View\AbstractView;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonApply;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonCancel;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\DeviceZoom\BlockDeviceZoom;
use Nextend\SmartSlider3\Application\Admin\Layout\LayoutIframe;
use Nextend\SmartSlider3Pro\Form\Element\Select\ShapeDividerSelect;

class ViewSliderShapeDivider extends AbstractView {

    /** @var integer */
    protected $sliderID;

    public function display() {
        $this->layout = new LayoutIframe($this);

        $this->layout->setLabel(n2_('Shape divider'));

        $deviceZoom = new BlockDeviceZoom($this);
        $this->layout->addAction($deviceZoom);

        $buttonCancel = new BlockButtonCancel($this);
        $buttonCancel->addAttribute('id', 'n2-ss-form-cancel');
        $buttonCancel->setBig();
        $this->layout->addAction($buttonCancel);

        $buttonSet = new BlockButtonApply($this);
        $buttonSet->addAttribute('id', 'n2-ss-form-save');
        $buttonSet->setBig();
        $this->layout->addAction($buttonSet);

        $this->layout->addContent($this->render('ShapeDivider'));

        $this->layout->render();
    }

    /**
     * @return integer
     */
    public function getSliderID() {
        return $this->sliderID;
    }

    /**
     * @param integer $sliderID
     */
    public function setSliderID($sliderID) {
        $this->sliderID = $sliderID;
    }


    public function renderForm() {

        $form = new Form($this, 'slider');

        $table = new ContainerTable($form->getContainer(), 'shapedivider', n2_('Shape divider'));

        $table->setFieldsetPositionEnd();

        new Tab($table->getFieldsetLabel(), 'position', false, 'bottom', array(
            'options'            => array(
                'top'    => n2_('Top'),
                'bottom' => n2_('Bottom')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'top'
                    ),
                    'field'  => array(
                        'table-row-shapedivider-top'
                    )
                ),
                array(
                    'values' => array(
                        'bottom'
                    ),
                    'field'  => array(
                        'table-row-shapedivider-bottom'
                    )
                )
            )
        ));


        $top = $table->createRow('shapedivider-top');
        new ShapeDividerSelect($top, 'shapedivider-top-type', n2_('Type'), '0', array(
            'relatedFields' => array(
                'slidershapedivider-top-group-container'
            )
        ));

        $groupingTopOptionsContainer = new Grouping($top, 'shapedivider-top-group-container');
        new Color($groupingTopOptionsContainer, 'shapedivider-top-color', n2_('Color'), 'ffffffff', array(
            'alpha' => true
        ));

        new Color($groupingTopOptionsContainer, 'shapedivider-top-color2', n2_('Secondary'), 'FFFFFF80', array(
            'alpha' => true
        ));

        new NumberSlider($groupingTopOptionsContainer, 'shapedivider-top-width', n2_('Width'), 100, array(
            'unit'          => '%',
            'style'         => 'width:35px;',
            'min'           => 100,
            'max'           => 400,
            'step'          => 5,
            'sliderMax'     => 400,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new NumberSlider($groupingTopOptionsContainer, 'shapedivider-top-height', n2_('Height'), 100, array(
            'unit'          => '%',
            'style'         => 'width:35px;',
            'min'           => 0,
            'max'           => 500,
            'step'          => 10,
            'sliderMax'     => 500,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new OnOff($groupingTopOptionsContainer, 'shapedivider-top-flip', n2_('Flip'), 0);
        new OnOff($groupingTopOptionsContainer, 'shapedivider-top-animate', n2_('Animate'), 0);
        new NumberSlider($groupingTopOptionsContainer, 'shapedivider-top-speed', n2_('Speed'), 100, array(
            'style'     => 'width:35px;',
            'unit'      => '%',
            'min'       => 10,
            'max'       => 1000,
            'step'      => 1,
            'sliderMax' => 100
        ));

        new Select($groupingTopOptionsContainer, 'shapedivider-top-scroll', n2_('Scroll'), '0', array(
            'options' => array(
                '0'      => n2_('None'),
                'grow'   => n2_('Grow'),
                'shrink' => n2_('Shrink')
            )
        ));

        $bottom = $table->createRow('shapedivider-bottom');
        new ShapeDividerSelect($bottom, 'shapedivider-bottom-type', n2_('Type'), '0', array(
            'relatedFields' => array(
                'slidershapedivider-bottom-group-container'
            )
        ));

        $groupingBottomOptionsContainer = new Grouping($bottom, 'shapedivider-bottom-group-container');
        new Color($groupingBottomOptionsContainer, 'shapedivider-bottom-color', n2_('Color'), 'ffffffff', array(
            'alpha' => true
        ));

        new Color($groupingBottomOptionsContainer, 'shapedivider-bottom-color2', n2_('Secondary'), 'FFFFFF80', array(
            'alpha' => true
        ));

        new NumberSlider($groupingBottomOptionsContainer, 'shapedivider-bottom-width', n2_('Width'), 100, array(
            'style'         => 'width:35px;',
            'unit'          => '%',
            'min'           => 100,
            'max'           => 400,
            'step'          => 5,
            'sliderMax'     => 400,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new NumberSlider($groupingBottomOptionsContainer, 'shapedivider-bottom-height', n2_('Height'), 100, array(
            'style'         => 'width:35px;',
            'unit'          => '%',
            'min'           => 0,
            'max'           => 500,
            'step'          => 10,
            'rowAttributes' => array(
                'data-devicespecific' => ''
            )
        ));

        new OnOff($groupingBottomOptionsContainer, 'shapedivider-bottom-flip', n2_('Flip'), 0);
        new OnOff($groupingBottomOptionsContainer, 'shapedivider-bottom-animate', n2_('Animate'), 0);
        new NumberSlider($groupingBottomOptionsContainer, 'shapedivider-bottom-speed', n2_('Speed'), 100, array(
            'style'     => 'width:35px;',
            'unit'      => '%',
            'min'       => 10,
            'max'       => 1000,
            'step'      => 1,
            'sliderMax' => 100
        ));

        new Select($groupingBottomOptionsContainer, 'shapedivider-bottom-scroll', n2_('Scroll'), '0', array(
            'options' => array(
                '0'      => n2_('None'),
                'grow'   => n2_('Grow'),
                'shrink' => n2_('Shrink')
            )
        ));


        echo $form->render();
    }
}