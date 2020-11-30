<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\Elements;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;

class YouTubePlaylistByUser extends Select {

    /** @var  N2SliderGeneratorYouTubeConfiguration */
    protected $config;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        try {
            $playlists = $this->config->getPlaylists($this->config->getApi(), $this->getForm()
                                                                                   ->get('channel-id', ''));

            foreach ($playlists as $k => $item) {
                $this->options[$item['id']] = $item['snippet']['title'];
            }

            if (!isset($this->options[$this->getValue()])) {
                $this->setValue($playlists[0]['id']);
            }

        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }


    }

    protected function fetchElement() {

        $getDataUrl = $this->getForm()
                           ->createAjaxUrl(array(
                               "generator/getData",
                               array(
                                   'group' => Request::$REQUEST->getVar('group'),
                                   'type'  => Request::$REQUEST->getVar('type')
                               )
                           ));

        Js::addInline('
            new N2Classes.FormElementYouTubePlaylists("' . $this->fieldID . '", "' . $getDataUrl . '");
        ');

        return parent::fetchElement();
    }

    /**
     * @param N2SliderGeneratorYouTubeConfiguration $config
     */
    public function setConfig($config) {
        $this->config = $config;
    }
}
