<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\NextgenGallery\Elements;

use Nextend\Framework\Form\Element\Select;

class NextgenGalleries extends Select {

    protected function fetchElement() {
        global $wpdb;

        $galleries = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "ngg_gallery ORDER BY name");

        if (count($galleries)) {
            foreach ($galleries AS $gallery) {
                $this->options[$gallery->gid] = $gallery->title;
            }

            if ($this->getValue() == '') {
                $this->setValue($galleries[0]->gid);
            }
        }

        return parent::fetchElement();
    }
}
