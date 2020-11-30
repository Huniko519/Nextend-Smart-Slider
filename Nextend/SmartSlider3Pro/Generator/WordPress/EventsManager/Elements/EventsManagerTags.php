<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Elements;

use Nextend\Framework\Form\Element\Select;


class EventsManagerTags extends Select {

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $categories = get_categories(array(
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'event-tags',
            'pad_counts'   => false
        ));
        $new        = array();
        foreach ($categories as $a) {
            $new[$a->category_parent][] = $a;
        }
        $options = array();
        $this->createTree($options, $new, 0);

        $this->options['0'] = n2_('All');
        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->cat_ID] = ' - ' . $option->treename;
            }
        }
    }
}
