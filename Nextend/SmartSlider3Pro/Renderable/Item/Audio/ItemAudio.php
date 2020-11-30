<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Audio;


use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Video;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemAudio extends AbstractItem {

    protected $ordering = 21;

    protected $layerProperties = array(
        "desktopportraitwidth" => 300
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'audio';
    }

    public function getTitle() {
        return n2_('Audio');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--audio';
    }

    public function getGroup() {
        return n2_x('Media', 'Layer group');
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemAudioFrontend($this, $id, $itemData, $layer);
    }

    /**
     * @return array
     */
    public function getValues() {
        return parent::getValues() + array(
                'audio_mp3'     => '',
                'volume'        => 1,
                'autoplay'      => 0,
                'loop'          => 0,
                'reset'         => 0,
                'color'         => '000000B2',
                'color2'        => 'ffffff',
                'videoplay'     => '',
                'videopause'    => '',
                'videoend'      => '',
                'fullwidth'     => 1,
                'show'          => 1,
                'show-progress' => 1,
                'show-time'     => 1,
                'show-volume'   => 1
            );
    }

    /**
     * @return string
     */


    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('audio', $slide->fill($data->get('audio', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('audio'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('audio', $import->fixImage($data->get('audio')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('audio', ResourceTranslator::toUrl($data->get('audio')));

        return $data;
    }

    public function loadResources($renderable) {
        parent::loadResources($renderable);

        $renderable->addLess(self::getAssetsPath() . "/audio.n2less", array(
            "sliderid" => $renderable->elementId
        ));
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-audio', n2_('General'));

        new Video($settings, 'audio_mp3', n2_('MP3 audio'), '', array(
            'width' => 220
        ));

        new Color($settings, 'color', n2_('Main color'), '', array(
            'alpha' => true
        ));
        new Color($settings, 'color2', n2_('Secondary color'));

        $audioSettings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-audio-settings', n2_('Audio settings'));
        new Warning($audioSettings, 'autoplay-notice', sprintf(n2_('Audio autoplaying has a lot of limitations made by browsers. You can read about them %1$shere%2$s.'), '<a href="https://smartslider.helpscoutdocs.com/article/1919-video-autoplay-handling" target="_blank">', '</a>'));

        new OnOff($audioSettings, 'autoplay', n2_('Autoplay'), 0, array(
            'relatedFieldsOn' => array(
                'item_audioautoplay-notice'
            )
        ));
        new OnOff($audioSettings, 'loop', n2_x('Loop', 'Video/Audio play'), 0);
        new Select($audioSettings, 'volume', n2_('Volume'), 1, array(
            'options' => array(
                '0'    => n2_('Mute'),
                '0.25' => '25%',
                '0.5'  => '50%',
                '0.75' => '75%',
                '1'    => '100%'
            )
        ));
        new OnOff($audioSettings, 'reset', n2_('Restart on slide change'), 0, array(
            'tipLabel'       => n2_('Restart on slide change'),
            'tipDescription' => n2_('Starts the audio from the beginning when the slide is viewed again.')
        ));

        $display = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-audio-display', n2_('Display'));
        new OnOff($display, 'fullwidth', n2_('Full width'), 0);
        new OnOff($display, 'show', n2_('Controls'), 0);
        new OnOff($display, 'show-progress', n2_('Progress'), 0);
        new OnOff($display, 'show-time', n2_('Time'), 0);
        new OnOff($display, 'show-volume', n2_('Volume'), 0);
    }
}