<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\BestWebSoft\Elements;

use Nextend\Framework\Form\Element\Select;

class BestWebSoftGalleries extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $galleries = get_posts(array(
            'post_type'    => 'bws-gallery',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'pad_counts'   => false
        ));

        $this->options[0] = 'Please select a gallery';

        if (count($galleries)) {
            foreach ($galleries AS $gallery) {
                $this->options[$gallery->ID] = $gallery->post_title;
            }
        }
    }
}
