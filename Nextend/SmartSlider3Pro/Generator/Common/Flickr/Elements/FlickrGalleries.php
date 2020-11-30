<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Flickr\Elements;

use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Notification\Notification;


class FlickrGalleries extends Select {

    /** @var  DPZFlickr */
    protected $api;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $result = $this->api->galleries_getList('');

        if (isset($result['stat']) && $result['stat'] == "fail") {
            Notification::error($result['message']);

            return false;
        }

        if (isset($result['galleries']) && isset($result['galleries']['gallery'])) {
            $galleries = $result['galleries']['gallery'];

            if (count($galleries)) {
                foreach ($galleries AS $gallery) {
                    $this->options[$gallery['id']] = $gallery['title']['_content'];
                }
                if ($this->getValue() == '') {
                    $this->setValue($galleries[0]['id']);
                }
            }
        }

    }

    public function setApi($api) {
        $this->api = $api;
    }
}
