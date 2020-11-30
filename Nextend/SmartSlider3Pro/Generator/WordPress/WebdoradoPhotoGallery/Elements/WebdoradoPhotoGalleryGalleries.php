<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Elements;

use Nextend\Framework\Form\Element\Select;


class WebdoradoPhotoGalleryGalleries extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        global $wpdb;
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $this->options['0'] = n2_('All');

        $galleries = $wpdb->get_results("SELECT id, name FROM " . $wpdb->base_prefix . "bwg_gallery WHERE published = 1");
        foreach ($galleries AS $gallery) {
            $this->options[$gallery->id] = $gallery->name;
        }
    }
}
