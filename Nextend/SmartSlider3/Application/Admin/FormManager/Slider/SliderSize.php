<?php


namespace Nextend\SmartSlider3\Application\Admin\FormManager\Slider;


use Nextend\Framework\Form\Container\ContainerRowGroup;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Breakpoint;
use Nextend\Framework\Form\Element\CheckboxOnOff;
use Nextend\Framework\Form\Element\Group\GroupCheckboxOnOff;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\Hidden\HiddenOnOff;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text\HiddenText;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\FormTabbed;
use Nextend\SmartSlider3\Application\Admin\Settings\ViewSettingsGeneral;
use Nextend\SmartSlider3\Form\Element\Select\ResponsiveSubFormIcon;
use Nextend\SmartSlider3\Settings;

class SliderSize extends AbstractSliderTab {


    /**
     * SliderSize constructor.
     *
     * @param FormTabbed $form
     */
    public function __construct($form) {
        parent::__construct($form);

        $this->size();
        $this->breakpoints();
        $this->layout();
        $this->customSize();
    }

    /**
     * @return string
     */
    protected function getName() {
        return 'size';
    }

    /**
     * @return string
     */
    protected function getLabel() {
        return n2_('Size');
    }

    protected function size() {

        $table = new ContainerTable($this->tab, 'size', n2_('Slider size'));


        /**
         * Used for field injection: /size/size/size-1
         */
        $row1 = $table->createRow('size-1');

        new NumberAutoComplete($row1, 'width', n2_('Width'), 900, array(
            'wide'   => 5,
            'min'    => 10,
            'values' => array(
                1920,
                1400,
                1000,
                800,
                600,
                400
            ),
            'unit'   => 'px'
        ));
        new NumberAutoComplete($row1, 'height', n2_('Height'), 500, array(
            'wide'   => 5,
            'min'    => 10,
            'values' => array(
                800,
                600,
                500,
                400,
                300,
                200
            ),
            'unit'   => 'px'
        ));

        $groupShowOn = new GroupCheckboxOnOff($row1, 'show-on', n2_('Hide on'));
        new CheckboxOnOff($groupShowOn, 'mobileportrait', false, 'ssi_16 ssi_16--mobileportrait', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Mobile')
        ));
        new CheckboxOnOff($groupShowOn, 'mobilelandscape', false, 'ssi_16 ssi_16--mobileportraitlarge', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Large mobile'),
            'rowClass'    => 'n2-slider-settings-require--mobilelandscape'
        ));
        new CheckboxOnOff($groupShowOn, 'tabletportrait', false, 'ssi_16 ssi_16--tabletportrait', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Tablet')
        ));
        new CheckboxOnOff($groupShowOn, 'tabletlandscape', false, 'ssi_16 ssi_16--tabletportraitlarge', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Large tablet'),
            'rowClass'    => 'n2-slider-settings-require--tabletlandscape'
        ));
        new CheckboxOnOff($groupShowOn, 'desktopportrait', false, 'ssi_16 ssi_16--desktopportrait', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Desktop')
        ));
        new CheckboxOnOff($groupShowOn, 'desktoplandscape', false, 'ssi_16 ssi_16--desktoplandscape', 1, array(
            'invert'      => true,
            'checkboxTip' => n2_('Large desktop'),
            'rowClass'    => 'n2-slider-settings-require--desktoplandscape'
        ));
    

        /**
         * Used for field removal: /size/size/size-2
         */
        $row2 = $table->createRow('size-2');

        new OnOff($row2, 'responsiveLimitSlideWidth', n2_('Limit slide width'), 1, array(
            'relatedFieldsOn' => array(
                'slidergrouping-responsive-slide-width'
            ),
            'tipLabel'        => n2_('Limit slide width'),
            'tipDescription'  => n2_('Limits the width of the slide and prevents the slider from getting too tall.'),
            'tipLink'         => 'https://smartslider.helpscoutdocs.com/article/1774-slider-settings-size#limit-slide-width'
        ));

        $slideMaxWidthGroup = new Grouping($row2, 'grouping-responsive-slide-width');
        $slideMaxWidthGroupDesktopLandscape = new Grouping($slideMaxWidthGroup, 'grouping-responsive-slide-width-desktop-landscape');

        new OnOff($slideMaxWidthGroupDesktopLandscape, 'responsiveSlideWidthDesktopLandscape', n2_('Large desktop'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMaxDesktopLandscape'
            )
        ));
        new NumberAutoComplete($slideMaxWidthGroupDesktopLandscape, 'responsiveSlideWidthMaxDesktopLandscape', n2_('Max'), 1600, array(
            'min'    => 0,
            'values' => array(
                3000,
                1600
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
    

        new OnOff($slideMaxWidthGroup, 'responsiveSlideWidth', n2_('Desktop'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMax'
            )
        ));
        new NumberAutoComplete($slideMaxWidthGroup, 'responsiveSlideWidthMax', n2_('Max'), 3000, array(
            'min'    => 0,
            'values' => array(
                3000,
                980
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
        $slideMaxWidthGroupTabletLandscape = new Grouping($slideMaxWidthGroup, 'grouping-responsive-slide-width-tablet-landscape');

        new OnOff($slideMaxWidthGroupTabletLandscape, 'responsiveSlideWidthTabletLandscape', n2_('Large tablet'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMaxTabletLandscape'
            )
        ));
        new NumberAutoComplete($slideMaxWidthGroupTabletLandscape, 'responsiveSlideWidthMaxTabletLandscape', n2_('Max'), 1200, array(
            'min'    => 0,
            'values' => array(
                3000,
                1200
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
    

        new OnOff($slideMaxWidthGroup, 'responsiveSlideWidthTablet', n2_('Tablet'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMaxTablet'
            )
        ));
        new NumberAutoComplete($slideMaxWidthGroup, 'responsiveSlideWidthMaxTablet', n2_('Max'), 3000, array(
            'min'    => 0,
            'values' => array(
                3000,
                980
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
        $slideMaxWidthGroupMobileLandscape = new Grouping($slideMaxWidthGroup, 'grouping-responsive-slide-width-mobile-landscape');

        new OnOff($slideMaxWidthGroupMobileLandscape, 'responsiveSlideWidthMobileLandscape', n2_('Large mobile'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMaxMobileLandscape'
            )
        ));

        new NumberAutoComplete($slideMaxWidthGroupMobileLandscape, 'responsiveSlideWidthMaxMobileLandscape', n2_('Max'), 740, array(
            'min'    => 0,
            'values' => array(
                3000,
                740
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));
    

        new OnOff($slideMaxWidthGroup, 'responsiveSlideWidthMobile', n2_('Mobile'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsiveSlideWidthMaxMobile'
            )
        ));
        new NumberAutoComplete($slideMaxWidthGroup, 'responsiveSlideWidthMaxMobile', n2_('Max'), 480, array(
            'min'    => 0,
            'values' => array(
                3000,
                480
            ),
            'unit'   => 'px',
            'wide'   => 5
        ));

    }

    protected function breakpoints() {

        $table = new ContainerTable($this->tab, 'breakpoints', n2_('Breakpoints'));

        $tableFieldset = $table->getFieldsetLabel();
        new HiddenText($tableFieldset, 'responsive-breakpoint-desktop-portrait', false, ViewSettingsGeneral::defaults['desktop-large-portrait']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-desktop-portrait-landscape', false, ViewSettingsGeneral::defaults['desktop-large-landscape']);

        new HiddenText($tableFieldset, 'responsive-breakpoint-tablet-landscape', false, ViewSettingsGeneral::defaults['tablet-large-portrait']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-tablet-landscape-landscape', false, ViewSettingsGeneral::defaults['tablet-large-landscape']);
    

        new HiddenText($tableFieldset, 'responsive-breakpoint-tablet-portrait', false, ViewSettingsGeneral::defaults['tablet-portrait']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-tablet-portrait-landscape', false, ViewSettingsGeneral::defaults['tablet-landscape']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-mobile-landscape', false, ViewSettingsGeneral::defaults['mobile-large-portrait']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-mobile-landscape-landscape', false, ViewSettingsGeneral::defaults['mobile-large-landscape']);
    

        new HiddenText($tableFieldset, 'responsive-breakpoint-mobile-portrait', false, ViewSettingsGeneral::defaults['mobile-portrait']);
        new HiddenText($tableFieldset, 'responsive-breakpoint-mobile-portrait-landscape', false, ViewSettingsGeneral::defaults['mobile-landscape']);
        new HiddenOnOff($tableFieldset, 'responsive-breakpoint-desktop-landscape-enabled', n2_('Large desktop'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsive-breakpoint-notice-desktop-landscape',
                'table-row-override-slider-size-desktop-landscape-row',
                'slidergrouping-responsive-slide-width-desktop-landscape'
            )
        ));
        new HiddenOnOff($tableFieldset, 'responsive-breakpoint-tablet-landscape-enabled', n2_('Large tablet'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsive-breakpoint-notice-tablet-landscape',
                'table-row-override-slider-size-tablet-landscape-row',
                'slidergrouping-responsive-slide-width-tablet-landscape'
            )
        ));
    
        new HiddenOnOff($tableFieldset, 'responsive-breakpoint-tablet-portrait-enabled', n2_('Tablet'), 1, array(
            'relatedFieldsOn' => array(
                'sliderresponsive-breakpoint-notice-tablet-portrait',
                'table-row-override-slider-size-tablet-portrait-row'
            )
        ));
        new HiddenOnOff($tableFieldset, 'responsive-breakpoint-mobile-landscape-enabled', n2_('Large mobile'), 0, array(
            'relatedFieldsOn' => array(
                'sliderresponsive-breakpoint-notice-mobile-landscape',
                'table-row-override-slider-size-mobile-landscape-row',
                'slidergrouping-responsive-slide-width-mobile-landscape'
            )
        ));
    
        new HiddenOnOff($tableFieldset, 'responsive-breakpoint-mobile-portrait-enabled', n2_('Mobile'), 1, array(
            'relatedFieldsOn' => array(
                'sliderresponsive-breakpoint-notice-mobile-portrait',
                'table-row-override-slider-size-mobile-portrait-row'
            )
        ));

        $row1 = $table->createRow('breakpoints-row-1');

        $instructions = n2_('Breakpoints define the browser width in pixel when the slider switches to a different device.');

        new Notice($row1, 'breakpoints-instructions', n2_('Instruction'), $instructions);

        $row2 = $table->createRow('breakpoints-row-2');

        new OnOff($row2, 'responsive-breakpoint-global', n2_('Global breakpoints'), 0, array(
            'tipLabel'       => n2_('Global breakpoints'),
            'tipDescription' => sprintf(n2_('You can use the global breakpoints, or adjust them locally here. You can configure the Global breakpoints at <a href="%1$s" target="_blank">Global settings</a> > General > Breakpoints'), $this->form->getMVCHelper()
                                                                                                                                                                                                                                                   ->getUrlSettingsDefault())
        ));
        new Breakpoint($row2, 'breakpoints', array(
            'desktoplandscape-portrait'  => 'sliderresponsive-breakpoint-desktop-portrait',
            'desktoplandscape-landscape' => 'sliderresponsive-breakpoint-desktop-portrait-landscape',
            'tabletlandscape-portrait'   => 'sliderresponsive-breakpoint-tablet-landscape',
            'tabletlandscape-landscape'  => 'sliderresponsive-breakpoint-tablet-landscape-landscape',
            'tabletportrait-portrait'    => 'sliderresponsive-breakpoint-tablet-portrait',
            'tabletportrait-landscape'   => 'sliderresponsive-breakpoint-tablet-portrait-landscape',
            'mobilelandscape-portrait'   => 'sliderresponsive-breakpoint-mobile-landscape',
            'mobilelandscape-landscape'  => 'sliderresponsive-breakpoint-mobile-landscape-landscape',
            'mobileportrait-portrait'    => 'sliderresponsive-breakpoint-mobile-portrait',
            'mobileportrait-landscape'   => 'sliderresponsive-breakpoint-mobile-portrait-landscape'
        ), array(
            'desktoplandscape' => 'sliderresponsive-breakpoint-desktop-landscape-enabled',
            'tabletlandscape'  => 'sliderresponsive-breakpoint-tablet-landscape-enabled',
            'mobilelandscape'  => 'sliderresponsive-breakpoint-mobile-landscape-enabled'
        ), array(
            'field'  => 'sliderresponsive-breakpoint-global',
            'values' => array(
                'desktoplandscape-portrait'  => Settings::get('responsive-screen-width-desktop-portrait', ViewSettingsGeneral::defaults['desktop-large-portrait']),
                'desktoplandscape-landscape' => Settings::get('responsive-screen-width-desktop-portrait-landscape', ViewSettingsGeneral::defaults['desktop-large-landscape']),
                'tabletlandscape-portrait'   => Settings::get('responsive-screen-width-tablet-landscape', ViewSettingsGeneral::defaults['tablet-large-portrait']),
                'tabletlandscape-landscape'  => Settings::get('responsive-screen-width-tablet-landscape-landscape', ViewSettingsGeneral::defaults['tablet-large-landscape']),
                'tabletportrait-portrait'    => Settings::get('responsive-screen-width-tablet-portrait', ViewSettingsGeneral::defaults['tablet-portrait']),
                'tabletportrait-landscape'   => Settings::get('responsive-screen-width-tablet-portrait-landscape', ViewSettingsGeneral::defaults['tablet-landscape']),
                'mobilelandscape-portrait'   => Settings::get('responsive-screen-width-mobile-landscape', ViewSettingsGeneral::defaults['mobile-large-portrait']),
                'mobilelandscape-landscape'  => Settings::get('responsive-screen-width-mobile-landscape-landscape', ViewSettingsGeneral::defaults['mobile-large-landscape']),
                'mobileportrait-portrait'    => Settings::get('responsive-screen-width-mobile-portrait', ViewSettingsGeneral::defaults['mobile-portrait']),
                'mobileportrait-landscape'   => Settings::get('responsive-screen-width-mobile-portrait-landscape', ViewSettingsGeneral::defaults['mobile-landscape'])
            )
        ));
    }

    protected function layout() {

        $table = new ContainerTable($this->tab, 'responsive-mode', n2_('Layout'));

        $row1 = $table->createRow('responsive-mode-row-1');

        /**
         * Used for option removal: /size/responsive-mode/responsive-mode-row-1/responsive-mode
         */
        new ResponsiveSubFormIcon($row1, 'responsive-mode', $table, $this->form->createAjaxUrl(array("slider/renderresponsivetype")), 'auto');

    }

    protected function customSize() {

        $table = new ContainerTable($this->tab, 'override-slider-size', n2_('Custom size'));

        new OnOff($table->getFieldsetLabel(), 'slider-size-override', '', 0, array(
            'relatedFieldsOn' => array(
                'table-row-group-override-slider-size'
            )
        ));

        $row1         = $table->createRow('size-1');
        $instructions = sprintf(n2_('Use this option to customize the aspect ratio for each device. %1$s Read more in the documentation%2$s. <b>Beware:</b> This option is rarely needed and might be hard to set properly!'), '<a href="https://smartslider.helpscoutdocs.com/article/1774-slider-settings-size#custom-size" target="_blank">', '</a>');
        new Notice($row1, 'instructions', n2_('Instruction'), $instructions);

        $overrideEditorSize = $table->createRowGroup('override-slider-size', false);

        $this->mobilePortrait($overrideEditorSize);
        $this->mobileLandscape($overrideEditorSize);
        $this->tabletPortrait($overrideEditorSize);
        $this->tabletLandscape($overrideEditorSize);
        $this->desktopLandscape($overrideEditorSize);


    
    }

    /**
     * @param ContainerRowGroup $rowGroup
     */
    protected function desktopLandscape($rowGroup) {

        $row = $rowGroup->createRow('override-slider-size-desktop-landscape-row');

        new OnOff($row, 'slider-size-override-desktop-landscape', n2_('Large desktop'), 0, array(
            'relatedFieldsOn' => array(
                'sliderdesktop-landscape-width',
                'sliderdesktop-landscape-height'
            )
        ));

        new Number($row, 'desktop-landscape-width', n2_('Width'), 1440, array(
            'wide' => 5,
            'unit' => 'px'
        ));
        new Number($row, 'desktop-landscape-height', n2_('Height'), 900, array(
            'wide' => 5,
            'unit' => 'px'
        ));
    
    }

    /**
     * @param ContainerRowGroup $rowGroup
     */
    protected function tabletLandscape($rowGroup) {

        $row = $rowGroup->createRow('override-slider-size-tablet-landscape-row');

        new OnOff($row, 'slider-size-override-tablet-landscape', n2_('Large tablet'), 0, array(
            'relatedFieldsOn' => array(
                'slidertablet-landscape-width',
                'slidertablet-landscape-height'
            )
        ));

        new Number($row, 'tablet-landscape-width', n2_('Width'), 1024, array(
            'wide' => 5,
            'unit' => 'px'
        ));
        new Number($row, 'tablet-landscape-height', n2_('Height'), 768, array(
            'wide' => 5,
            'unit' => 'px'
        ));
    
    }

    /**
     * @param ContainerRowGroup $rowGroup
     */
    protected function tabletPortrait($rowGroup) {

        $row = $rowGroup->createRow('override-slider-size-tablet-portrait-row');

        new OnOff($row, 'slider-size-override-tablet-portrait', n2_('Tablet'), 0, array(
            'relatedFieldsOn' => array(
                'slidertablet-portrait-width',
                'slidertablet-portrait-height'
            )
        ));

        new Number($row, 'tablet-portrait-width', n2_('Width'), 768, array(
            'wide' => 5,
            'unit' => 'px'
        ));
        new Number($row, 'tablet-portrait-height', n2_('Height'), 1024, array(
            'wide' => 5,
            'unit' => 'px'
        ));
    }

    /**
     * @param ContainerRowGroup $rowGroup
     */
    protected function mobileLandscape($rowGroup) {

        $row = $rowGroup->createRow('override-slider-size-mobile-landscape-row');

        new OnOff($row, 'slider-size-override-mobile-landscape', n2_('Large mobile'), 0, array(
            'relatedFieldsOn' => array(
                'slidermobile-landscape-width',
                'slidermobile-landscape-height'
            )
        ));

        new Number($row, 'mobile-landscape-width', n2_('Width'), 568, array(
            'wide' => 5,
            'unit' => 'px'
        ));
        new Number($row, 'mobile-landscape-height', n2_('Height'), 320, array(
            'wide' => 5,
            'unit' => 'px'
        ));
    
    }

    /**
     * @param ContainerRowGroup $rowGroup
     */
    protected function mobilePortrait($rowGroup) {

        $row = $rowGroup->createRow('override-slider-size-mobile-portrait-row');

        new OnOff($row, 'slider-size-override-mobile-portrait', n2_('Mobile'), 0, array(
            'relatedFieldsOn' => array(
                'slidermobile-portrait-width',
                'slidermobile-portrait-height'
            )
        ));

        new Number($row, 'mobile-portrait-width', n2_('Width'), 320, array(
            'wide' => 5,
            'unit' => 'px'
        ));
        new Number($row, 'mobile-portrait-height', n2_('Height'), 568, array(
            'wide' => 5,
            'unit' => 'px'
        ));
    }
}