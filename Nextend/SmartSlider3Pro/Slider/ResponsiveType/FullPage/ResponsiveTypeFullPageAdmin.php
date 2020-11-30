<?php

namespace Nextend\SmartSlider3Pro\Slider\ResponsiveType\FullPage;

use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Slider\ResponsiveType\AbstractResponsiveTypeAdmin;

class ResponsiveTypeFullPageAdmin extends AbstractResponsiveTypeAdmin {

    protected $ordering = 3;

    public function getLabel() {

        return n2_('Full page');
    }

    public function getIcon() {
        return 'ssi_64 ssi_64--stretch';
    }

    public function renderFields($container) {

        $row1 = new FieldsetRow($container, 'responsive-fullpage-1');

        new OnOff($row1, 'responsiveForceFull', n2_('Force full width'), 1, array(
            'tipLabel'       => n2_('Force full width'),
            'tipDescription' => n2_('The slider tries to fill the full width of the browser.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#force-full-width'
        ));

        new Select($row1, 'responsiveForceFullOverflowX', n2_('Overflow-X'), 'body', array(
            'options'        => array(
                'body' => 'body',
                'html' => 'html',
                'none' => n2_('None')
            ),
            'tipLabel'       => n2_('Overflow-X'),
            'tipDescription' => n2_('Prevents the vertical scrollbar from appear during certain slide background animations.')
        ));

        new Text($row1, 'responsiveForceFullHorizontalSelector', n2_('Adjust slider width to'), 'body', array(
            'tipLabel'       => n2_('Adjust slider width to'),
            'tipDescription' => n2_('You can make the slider fill up a selected parent element instead of the full browser width.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#adjust-slider-width-to'
        ));
        new OnOff($row1, 'responsiveConstrainRatio', n2_('Constrain ratio'), 0, array(
            'tipLabel'       => n2_('Constrain ratio'),
            'tipDescription' => n2_('The slide scales horizontally and vertically with the same amount.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#constrain-ratio'
        ));

        $row2 = new FieldsetRow($container, 'responsive-fullpage-2');

        new Select($row2, 'sliderHeightBasedOn', n2_('Height based on'), 'real', array(
            'options'        => array(
                'real'  => 'Real height',
                '100vh' => 'CSS 100vh'
            ),
            'tipLabel'       => n2_('Height based on'),
            'tipDescription' => n2_('The real height makes your slider have the height of the browser without the URL bar, while the CSS 100vh makes it exactly as big as the browser height.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#height-based-on'
        ));

        $form = $container->getForm();
        if (!$form->has('responsive-focus') && $form->has('responsiveHeightOffset')) {
            $old = $form->get('responsiveHeightOffset');

            $oldDefault = '';
            $oldDefault = '#wpadminbar';
        

            if ($old !== $oldDefault) {
                $form->set('responsive-focus', 1);
                $form->set('responsive-focus-top', $old);
            }
        }


        new Number($row2, 'responsiveDecreaseSliderHeight', n2_('Decrease height'), 0, array(
            'unit'           => 'px',
            'wide'           => 4,
            'tipLabel'       => n2_('Decrease height'),
            'tipDescription' => n2_('You can make your slider smaller than the full height of the browser by a given pixel, for example, to fit below your menu without causing scrollbar.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#decrease-height-by-selectors'
        ));

        new Select($row2, 'responsive-focus', n2_('Decrease height by selectors'), 0, array(
            'options'        => array(
                0 => n2_('Use global focus selectors'),
                1 => n2_('Use local selectors')
            ),
            'relatedFields'  => array(
                'sliderresponsive-focus-top',
                'sliderresponsive-focus-bottom'
            ),
            'tipLabel'       => n2_('Decrease height by selectors'),
            'tipDescription' => n2_('You can make your slider smaller than the full height of the browser, for example, to fit below your menu without causing scrollbar.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1777-fullpage-layout#decrease-height-by-selectors'
        ));
        new Text($row2, 'responsive-focus-top', n2_('Top') . ' - ' . n2_('CSS selector (sum of heights)'), '', array(
            'style' => 'width:400px;'
        ));
        new Text($row2, 'responsive-focus-bottom', n2_('Bottom') . ' - ' . n2_('CSS selector (sum of heights)'), '', array(
            'style' => 'width:400px;'
        ));
    }
}