<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Elements;

use Nextend\Framework\Form\Element\Select;
use Vimeo\Vimeo;


class VimeoAlbums extends Select {

    /** @var  Vimeo */
    protected $api;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);


        $response = $this->api->request('/me/albums', array(
            'per_page' => 100
        ));

        if ($response['status'] == 200) {
            $albums = $response['body']['data'];
            foreach ($albums AS $album) {
                $this->options[$album['uri']] = $album['name'];
            }

            if (!isset($this->options[$this->getValue()])) {
                $this->setValue($albums[0]['uri']);
            }
        }
    }

    /**
     * @param Vimeo $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

}
