<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Video;


use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Video;
use Nextend\Framework\Form\Fieldset;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Renderable\Item\AbstractItem;

class ItemVideo extends AbstractItem {

    protected $ordering = 20;

    protected $layerProperties = array(
        "desktopportraitwidth"  => 300,
        "desktopportraitheight" => 'auto'
    );

    protected function isBuiltIn() {
        return true;
    }

    public function getType() {
        return 'video';
    }

    public function getTitle() {
        return n2_('Video');
    }

    public function getIcon() {
        return 'ssi_32 ssi_32--video';
    }

    public function getGroup() {
        return n2_x('Media', 'Layer group');
    }

    /**
     * @param Data $data
     */
    public function upgradeData($data) {
        if (!$data->has('aspect-ratio')) {
            $data->set('aspect-ratio', 'fill');
        }
    }

    public function createFrontend($id, $itemData, $layer) {
        return new ItemVideoFrontend($this, $id, $itemData, $layer);
    }

    /**
     * @return array
     */
    public function getValues() {
        return parent::getValues() + array(
                'autoplay'     => 0,
                'video_mp4'    => '',
                'aspect-ratio' => '16:9',
                'scroll-pause' => 'partly-visible',
                'showcontrols' => 1,
                'volume'       => 1,
                'center'       => 0,
                'loop'         => 0,
                'reset'        => 0,
                'videoplay'    => '',
                'videopause'   => '',
                'videoend'     => '',
                'ended'        => ''
            );
    }

    public function getFilled($slide, $data) {
        $data = parent::getFilled($slide, $data);

        $data->set('poster', $slide->fill($data->get('poster', '')));
        $data->set('video_mp4', $slide->fill($data->get('video_mp4', '')));

        return $data;
    }

    public function prepareExport($export, $data) {
        parent::prepareExport($export, $data);

        $export->addImage($data->get('poster'));
        $export->addImage($data->get('video_mp4'));
    }

    public function prepareImport($import, $data) {
        $data = parent::prepareImport($import, $data);

        $data->set('poster', $import->fixImage($data->get('poster')));
        $data->set('video_mp4', $import->fixImage($data->get('video_mp4')));

        return $data;
    }

    public function prepareSample($data) {
        $data->set('poster', ResourceTranslator::toUrl($data->get('poster')));
        $data->set('video_mp4', ResourceTranslator::toUrl($data->get('video_mp4')));

        return $data;
    }

    public function renderFields($container) {
        $settings = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-video', n2_('General'));

        new Video($settings, 'video_mp4', n2_('MP4 video'), '', array(
            'width' => 220
        ));

        new FieldImage($settings, 'poster', n2_('Cover image'), '', array(
            'width' => 220
        ));

        new Select($settings, 'aspect-ratio', n2_('Aspect ratio'), '16:9', array(
            'options'            => array(
                '16:9'   => '16:9',
                '16:10'  => '16:10',
                '4:3'    => '4:3',
                'custom' => n2_('Custom'),
                'fill'   => n2_('Fill layer height')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'custom'
                    ),
                    'field'  => array(
                        'item_videoaspect-ratio-width',
                        'item_videoaspect-ratio-height'
                    )
                ),
                array(
                    'values' => array(
                        'fill'
                    ),
                    'field'  => array(
                        'item_videoaspect-ratio-notice',
                        'item_videocenter'
                    )
                )
            )
        ));

        new Text\Number($settings, 'aspect-ratio-width', n2_('Width'), '16', array(
            'wide' => 4,
            'min'  => 1
        ));

        new Text\Number($settings, 'aspect-ratio-height', n2_('Height'), '9', array(
            'wide' => 4,
            'min'  => 1
        ));

        new Notice($settings, 'aspect-ratio-notice', n2_('Fill layer height'), n2_('Set on Style tab.'));

        $misc = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-video-misc', n2_('Video settings'));

        new Warning($misc, 'slide-background-notice', sprintf(n2_('Video autoplaying has a lot of limitations made by browsers. %1$sLearn about them.%2$s'), '<a href="https://smartslider.helpscoutdocs.com/article/1919-video-autoplay-handling" target="_blank">', '</a>'));

        new OnOff($misc, 'autoplay', n2_('Autoplay'), 0, array(
            'relatedFieldsOn' => array(
                'item_videoautoplay-notice'
            )
        ));

        new Select($misc, 'ended', n2_('When ended'), '', array(
            'options' => array(
                ''     => n2_('Do nothing'),
                'next' => 'Go to next slide'
            )
        ));

        new OnOff($misc, 'loop', n2_x('Loop', 'Video/Audio play'), 0, array(
            'relatedFieldsOff' => array(
                'item_videoended'
            )
        ));

        new Select($misc, 'volume', n2_('Volume'), 1, array(
            'options' => array(
                '0'    => n2_('Mute'),
                '0.25' => '25%',
                '0.5'  => '50%',
                '0.75' => '75%',
                '1'    => '100%'
            )
        ));

        new OnOff($misc, 'reset', n2_('Restart on slide change'), 0, array(
            'tipLabel'       => n2_('Restart on slide change'),
            'tipDescription' => n2_('Starts the video from the beginning when the slide is viewed again.')
        ));

        $display = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-video-display', n2_('Display'));

        new Select($display, 'fill-mode', n2_('Fill mode'), 'cover', array(
            'options' => array(
                'cover'   => n2_('Fill'),
                'contain' => n2_('Fit')
            )
        ));

        new OnOff($display, 'showcontrols', n2_('Controls'), 0);

        new OnOff($display, 'center', n2_('Centered'), 0);

        $load = new Fieldset\LayerWindow\FieldsetLayerWindow($container, 'item-video-dev', n2_('Loading'));

        new Select($load, 'preload', n2_('Preload'), 'metadata', array(
            'options' => array(
                'auto'     => 'Auto',
                'metadata' => 'metadata',
                'none'     => n2_('None')
            )
        ));

        new Select($load, 'scroll-pause', n2_('Pause on scroll'), 'partly-visible', array(
            'options'        => array(
                ''               => n2_('Never'),
                'partly-visible' => n2_('When partly visible'),
                'not-visible'    => n2_('When not visible'),
            ),
            'tipLabel'       => n2_('Pause on scroll'),
            'tipDescription' => n2_('You can pause the video when the visitor scrolls away from the slider')
        ));
    }
}