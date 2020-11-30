<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Iframe;


use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\Mixed;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Fieldset;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemIframe extends AbstractItem {

    protected $ordering = 100;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 300
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'iframe';
    }

    public function getTitle() {
        return n2_('iframe');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--iframe';
    }

    public function getGroup() {
        return n2_x('Advanced', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemIframeFrontend($this, $id, $itemData, $layer);
    }

    public function getValues() {
        return parent::getValues() + array(
                'url'    => 'https://smartslider3.com/',
                'size'   => '100%|*|100%',
                'scroll' => 'yes'
            );
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('url', $slide->fill($data->get('url', '')));

        return $data;
    }

    public function renderFields($container) {
        $general = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-iframe', n2_('General'));
        new Warning($general, '', n2_('Please note, that <b>we do not support</b> customized coding! The iframe layer often needs code customizations what you have to do yourself, so we only suggest using this layer if you are a developer!'));

        new Text($general, 'url', n2_('iframe url'), '', array(
            'style' => 'width:302px;'
        ));

        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-iframe-settings', n2_('Display'));
        new Select($settings, 'scroll', n2_('Scroll'), 'auto', array(
            'options'        => array(
                'yes'  => n2_('Yes'),
                'no'   => n2_('No'),
                'auto' => n2_('Auto')
            ),
            'tipLabel'       => n2_('Scroll'),
            'tipDescription' => n2_('You can disable the scroll on the iframe content.')
        ));
        $size = new Mixed($settings, 'size', false, '100%|*|100%');
        new Text($size, 'size-1', n2_('Width'), '', array(
            'style' => 'width:40px;'
        ));
        new Text($size, 'size-2', n2_('Height'), '', array(
            'style' => 'width:40px;'
        ));

        $advanced = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-iframe-advanced', n2_('Advanced'));

        new Text($advanced, 'title', n2_('iframe title'), '', array(
            'style' => 'width:302px;'
        ));
    }
}