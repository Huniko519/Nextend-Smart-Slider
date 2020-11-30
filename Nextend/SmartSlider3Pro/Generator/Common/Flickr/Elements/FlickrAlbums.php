<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Flickr\Elements;

use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Notification\Notification;


class FlickrAlbums extends Select {

    /** @var  DPZFlickr */
    protected $api;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $result = $this->api->photosets_getList('');

        if (isset($result['stat']) && $result['stat'] == "fail") {
            Notification::error($result['message']);

            return false;
        }
        if (isset($result['photosets']) && isset($result['photosets']['photoset'])) {
            $photoSets = $result['photosets']['photoset'];
            if (count($photoSets)) {
                foreach ($photoSets AS $set) {
                    $this->options[$set['id']] = $set['title']['_content'];
                }
                if ($this->getValue() == '') {
                    $this->setValue($photoSets[0]['id']);
                }
            }
        }
    }

    public function setApi($api) {
        $this->api = $api;
    }
}
