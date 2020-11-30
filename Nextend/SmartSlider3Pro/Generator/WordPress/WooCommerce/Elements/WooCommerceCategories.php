<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Elements;

use Nextend\Framework\Form\Element\Select;

class WooCommerceCategories extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $args       = array(
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'product_cat',
            'pad_counts'   => false
        );
        $categories = get_categories($args);
        $new        = array();
        foreach ($categories as $a) {
            $new[$a->category_parent][] = $a;
        }

        $options = array();
        $this->createTree($options, $new, 0);

        $this->options['0'] = n2_('Root');
        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->cat_ID] = ' - ' . $option->treename;
            }
        }
    }
}
