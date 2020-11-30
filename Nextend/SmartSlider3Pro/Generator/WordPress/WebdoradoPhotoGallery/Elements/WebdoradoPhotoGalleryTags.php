<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Elements;

use Nextend\Framework\Form\Element\Select;


class WebdoradoPhotoGalleryTags extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        global $wpdb;
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $this->options['0'] = n2_('All');

        $tags = $wpdb->get_results("SELECT term_id, name FROM wp_terms WHERE term_id IN (SELECT term_id FROM wp_term_taxonomy WHERE taxonomy = 'bwg_tag')");
        foreach ($tags AS $tag) {
            $this->options[$tag->term_id] = $tag->name;
        }
    }
}
