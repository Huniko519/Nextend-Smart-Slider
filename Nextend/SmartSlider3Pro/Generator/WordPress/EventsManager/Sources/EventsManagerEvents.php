<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select\Filter;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Elements\EventsManagerCategories;
use Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Elements\EventsManagerLocations;
use Nextend\SmartSlider3Pro\Generator\WordPress\EventsManager\Elements\EventsManagerTags;

class EventsManagerEvents extends AbstractGenerator {

    protected $layout = 'event';

    private $order;

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s events.'), 'Events Manager');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');

        new EventsManagerCategories($filter, 'categories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new EventsManagerTags($filter, 'tags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));
        new EventsManagerLocations($filter, 'locations', n2_('Location'), 0, array(
            'isMultiple' => true
        ));
        new Textarea($filter, 'exclude_ids', n2_('Exclude event IDs (One ID per line)'), '', array(
            'width'  => 150,
            'height' => 160
        ));

        $limit = $filterGroup->createRow('limit');
        new Filter($limit, 'ended', n2_('Ended'), -1);
        new Filter($limit, 'started', n2_('Started'), 0);
        new Text($limit, 'locationtown', n2_('Location town'), '');
        new Text($limit, 'locationstate', n2_('Location state'), '');

        if (is_multisite()) {
            $multiSite = $filterGroup->createRow('multisite');
            new OnOff($multiSite, 'multisite', n2_('Get all multisite events'), 0, array(
                'relatedFieldsOn' => array(
                    'generatormultiorder',
                    'generatorslidecount'
                )
            ));
            new OnOff($multiSite, 'multiorder', n2_('Order result'), 0);
            new OnOff($multiSite, 'slidecount', n2_('Events per site'), 1);
        }

        $dates = $filterGroup->createRow('dates');

        new Text($dates, 'date_variables', n2_('Format date variables'), '', array(
            'tipLabel'       => n2_('Format date variables'),
            'tipDescription' => n2_('With the WordPress Settings -> General -> Timezone and Date format, new variables will be generated from the given variables. Separate them with comma.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1893-wordpress-events-manager-generator#configuration-9'
        ));

        new OnOff($dates, 'custom_start_time', n2_('All day event') . ' - ' . n2_('Start time text'), 0, array(
            'relatedFieldsOn' => array(
                'generatorstart_time'
            )
        ));
        new Text($dates, 'start_time', n2_('Start time text'), '', array('post' => 'break'));
        new OnOff($dates, 'custom_end_date', n2_('All day event') . ' - ' . n2_('End date text'), 0, array(
            'relatedFieldsOn' => array(
                'generatorend_date'
            )
        ));
        new Text($dates, 'end_date', n2_('End date text'), '', array('post' => 'break'));
        new OnOff($dates, 'custom_end_time', n2_('All day event') . ' - ' . n2_('End time text'), 0, array(
            'relatedFieldsOn' => array(
                'generatorend_time'
            )
        ));
        new Text($dates, 'end_time', n2_('End time text'), '');

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order-row');
        new GeneratorOrder($order, 'order', 'event_start_date|*|asc', array(
            'options' => array(
                'default'             => n2_('Default'),
                'event_start_date'    => n2_('Start date'),
                'event_end_date'      => n2_('End date'),
                'event_rsvp_date'     => n2_('Rsvp date'),
                'event_date_created'  => n2_('Creation date'),
                'event_date_modified' => n2_('Modification date'),
                'post_title'          => n2_('Title'),
                'ID'                  => n2_('ID')
            )
        ));
    }

    protected function resetState() {
        $this->order = array(
            '_event_start_date',
            'asc'
        );
    }

    protected function eventTimes($event_all_day, $event_start_time, $event_end_time, $event_start_date, $event_end_date) {
        $replace = '';
        if (function_exists('get_option')) {
            $start = strtotime($event_start_date . " " . $event_start_time);
            $end   = strtotime($event_end_date . " " . $event_end_time);
            if (!$event_all_day) {
                $time_format = (get_option('dbem_time_format')) ? get_option('dbem_time_format') : get_option('time_format');
                if ($event_start_time != $event_end_time) {
                    $replace = date_i18n($time_format, $start) . get_option('dbem_times_separator') . date_i18n($time_format, $end);
                } else {
                    $replace = date_i18n($time_format, $start);
                }
            } else {
                $replace = get_option('dbem_event_all_day_message');
            }
        }

        return $replace;
    }

    protected function eventDates($event_start_time, $event_end_time, $event_start_date, $event_end_date) {
        $replace = '';
        if (function_exists('get_option')) {
            $start       = strtotime($event_start_date . " " . $event_start_time);
            $end         = strtotime($event_end_date . " " . $event_end_time);
            $date_format = (get_option('dbem_date_format')) ? get_option('dbem_date_format') : get_option('date_format');
            if ($event_start_date != $event_end_date) {
                $replace = date_i18n($date_format, $start) . get_option('dbem_dates_separator') . date_i18n($date_format, $end);
            } else {
                $replace = date_i18n($date_format, $start);
            }
        }

        return $replace;
    }

    protected function _getData($count, $startIndex) {

        global $wpdb;
        $data = array();

        $tax_query  = array();
        $meta_query = array();

        $categories = explode('||', $this->data->get('categories', 0));
        if (!in_array(0, $categories)) {
            $tax_query[] = array(
                'taxonomy' => 'event-categories',
                'field'    => 'term_id',
                'terms'    => $categories
            );
        }

        $tags = explode('||', $this->data->get('tags', 0));
        if (!in_array(0, $tags)) {
            $tax_query[] = array(
                'taxonomy' => 'event-tags',
                'field'    => 'term_id',
                'terms'    => $tags
            );
        }

        $locations    = explode('||', $this->data->get('locations', 0));
        $locationTown = str_replace(",", "','", $this->data->get('locationtown', ''));
        if (!empty($locationTown)) {
            $locationTown = "'" . $locationTown . "'";
        }
        $locationState = str_replace(",", "','", $this->data->get('locationstate', ''));
        if (!empty($locationState)) {
            $locationState = "'" . $locationState . "'";
        }

        if (!in_array(0, $locations)) {
            $query = "SELECT location_id FROM " . $wpdb->base_prefix . "em_locations WHERE post_id IN (" . implode(',', $locations) . ")";
            if (!empty($locationTown)) {
                $query .= " AND location_town IN (" . $locationTown . ")";
            }
            if (!empty($locationState)) {
                $query .= " AND location_state IN (" . $locationState . ")";
            }
        } else {
            if (!empty($locationTown)) {
                $query = "SELECT location_id FROM " . $wpdb->base_prefix . "em_locations WHERE location_town IN (" . $locationTown . ")";
                if (!empty($locationState)) {
                    $query .= " AND location_state IN (" . $locationState . ")";
                }
            } else if (!empty($locationState)) {
                $query = "SELECT location_id FROM " . $wpdb->base_prefix . "em_locations WHERE location_state IN (" . $locationState . ")";
            }
        }

        if (isset($query)) {
            $locations   = $wpdb->get_results($query);
            $locationIDs = array();
            for ($i = 0; $i < count($locations); $i++) {
                $locationIDs[$i] = $locations[$i]->location_id;
            }
            if (count($locationIDs)) {
                $meta_query[] = array(
                    'key'   => '_location_id',
                    'value' => $locationIDs
                );
            } else {
                return null;
            }
        }

        $today = date('Y-m-d H:i:s', current_time('timestamp'));

        switch ($this->data->get('started', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'     => '_event_start_local',
                    'value'   => $today,
                    'compare' => '<'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_event_start_local',
                    'value'   => $today,
                    'compare' => '>='
                );
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $meta_query[] = array(
                    'key'     => '_event_end_local',
                    'value'   => $today,
                    'compare' => '<'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_event_end_local',
                    'value'   => $today,
                    'compare' => '>='
                );
                break;
        }

        $args = array(
            'offset'           => $startIndex,
            'posts_per_page'   => $count,
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => false,
            'post_type'        => 'event',
            'tax_query'        => $tax_query,
            'meta_query'       => $meta_query
        );

        $exclude_ids = array_diff($this->getIDs('exclude_ids'), array(0));
        if (count($exclude_ids) > 0) {
            $args += array(
                'post__not_in' => $exclude_ids
            );
        }

        $this->order = Common::parse($this->data->get('order', 'event_start_date|*|asc'));
        if ($this->order[0] != 'default') {
            if ($this->order[0] == 'event_start_date' || $this->order[0] == 'event_end_date' || $this->order[0] == 'event_rsvp_date') {
                $meta_key = "_" . $this->order[0];
                $args     += array(
                    'ignore_custom_sort' => true,
                    'orderby'            => 'meta_value',
                    'meta_key'           => $meta_key,
                    'order'              => $this->order[1]
                );
            } else if ($this->order[0] == 'post_title' || $this->order[0] == 'ID') {
                $args += array(
                    'ignore_custom_sort' => true,
                    'orderby'            => $this->order[0],
                    'order'              => $this->order[1]
                );
            } else if ($this->order[0] == 'event_date_created') {
                $args += array(
                    'ignore_custom_sort' => true,
                    'orderby'            => 'post_date',
                    'order'              => $this->order[1]
                );
            } else if ($this->order[0] == 'event_date_modified') {
                $args += array(
                    'ignore_custom_sort' => true,
                    'orderby'            => 'post_modified',
                    'order'              => $this->order[1]
                );
            }
        }

        $multi = $this->data->get('multisite', 0);
        if (!$multi) {
            $posts = get_posts($args);
        } else {
            $original_blog = get_current_blog_id();
            $posts         = array();
            $blog_list     = get_blog_list(0, 'all');
            foreach ($blog_list as $blog) {
                switch_to_blog($blog['blog_id']);
                $current_blog = $blog['blog_id'];
                $newposts     = get_posts($args);
                for ($i = 0; $i < count($newposts); $i++) {
                    $newposts[$i]->blog_id = $blog['blog_id'];
                }
                $posts = array_merge($posts, $newposts);
            }
        }

        $custom_start_time = $this->data->get('custom_start_time', 0);
        $custom_end_date   = $this->data->get('custom_end_date', 0);
        $custom_end_time   = $this->data->get('custom_end_time', 0);
        $start_time        = $this->data->get('start_time', '');
        $end_date          = $this->data->get('end_date', '');
        $end_time          = $this->data->get('end_time', '');

        if ($multi && $this->data->get('multiorder', 0)) usort($posts, array(
            $this,
            'sortFunction'
        ));

        $dateVariables = array_map('trim', explode(',', $this->data->get('date_variables', '')));

        for ($i = 0; $i < count($posts); $i++) {
            $post        = $posts[$i];
            $EM_Event    = em_get_event($post->ID, 'post_id');
            $EM_Location = $EM_Event->get_location();
            $EM_Ticket   = $EM_Event->get_tickets();

            if ($multi && ($current_blog != $post->blog_id)) {
                switch_to_blog($post->blog_id);
                $current_blog = $post->blog_id;
            }
            //post data
            $data[$i]['title']       = $post->post_title;
            $data[$i]['description'] = $post->post_content;
            $data[$i]['excerpt']     = $post->post_excerpt;
            $data[$i]['image']       = ResourceTranslator::urlToResource(wp_get_attachment_url(get_post_thumbnail_id($post->ID)));
            $thumbnail               = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID, 'thumbnail'));
            if ($thumbnail[0]) {
                $data[$i]['thumbnail'] = ResourceTranslator::urlToResource($thumbnail[0]);
            } else {
                $data[$i]['thumbnail'] = $data[$i]['image'];
            }
            $data[$i]['url'] = get_permalink($post->ID);

            $start                  = strtotime($EM_Event->event_start_date . ' ' . $EM_Event->event_start_time);
            $data[$i]['start_date'] = date_i18n(get_option('date_format'), $start);
            if ($post->event_all_day && $custom_start_time) {
                $data[$i]['start_time'] = $start_time;
            } else {
                $data[$i]['start_time'] = date_i18n(get_option('time_format'), $start);
            }

            $end = strtotime($EM_Event->event_end_date . ' ' . $EM_Event->event_end_time);
            if ($post->event_all_day && $custom_end_date) {
                $data[$i]['end_date'] = $end_date;
            } else {
                $data[$i]['end_date'] = date_i18n(get_option('date_format'), $end);
            }

            if ($post->event_all_day && $custom_end_time) {
                $data[$i]['end_time'] = $end_time;
            } else {
                $data[$i]['end_time'] = date_i18n(get_option('time_format'), $end);
            }

            $data[$i]['event_times'] = $this->eventTimes($EM_Event->event_all_day, $EM_Event->event_start_time, $EM_Event->event_end_time, $EM_Event->event_start_date, $EM_Event->event_end_date);
            $data[$i]['event_dates'] = $this->eventDates($EM_Event->event_start_time, $EM_Event->event_end_time, $EM_Event->event_start_date, $EM_Event->event_end_date);

            $data[$i]['ID'] = $post->ID;

            $rsvp                  = strtotime($post->event_rsvp_date . ' ' . $post->event_rsvp_time);
            $data[$i]['rsvp_date'] = date_i18n(get_option('date_format'), $rsvp);
            $data[$i]['rsvp_time'] = date_i18n(get_option('time_format'), $rsvp);

            $data[$i]['rsvp_spaces']        = $post->event_rsvp_spaces;
            $data[$i]['spaces']             = $post->event_spaces;
            $data[$i]['location_name']      = $EM_Location->location_name;
            $data[$i]['location_address']   = $EM_Location->location_address;
            $data[$i]['location_town']      = $EM_Location->location_town;
            $data[$i]['location_image']     = ResourceTranslator::urlToResource(wp_get_attachment_url(get_post_thumbnail_id($EM_Location->post_id)));
            $data[$i]['location_state']     = $EM_Location->location_state;
            $data[$i]['location_postcode']  = $EM_Location->location_postcode;
            $data[$i]['location_region']    = $EM_Location->location_region;
            $data[$i]['location_country']   = $EM_Location->location_country;
            $data[$i]['location_latitude']  = $EM_Location->location_latitude;
            $data[$i]['location_longitude'] = $EM_Location->location_longitude;
            $data[$i]['ticket_name']        = $EM_Ticket->ticket_name;
            $data[$i]['ticket_description'] = $EM_Ticket->ticket_description;
            $data[$i]['ticket_price']       = $EM_Ticket->ticket_price;
            $data[$i]['ticket_start']       = $EM_Ticket->ticket_start;
            $data[$i]['ticket_end']         = $EM_Ticket->ticket_end;
            $data[$i]['ticket_min']         = $EM_Ticket->ticket_min;
            $data[$i]['ticket_max']         = $EM_Ticket->ticket_max;
            $data[$i]['ticket_spaces']      = $EM_Ticket->ticket_spaces;

            if (taxonomy_exists('event-categories')) {
                $categories = get_the_terms($post->ID, 'event-categories');
                if (isset($categories[0])) {
                    $data[$i]['category_name'] = $categories[0]->name;
                    $data[$i]['category_link'] = get_term_link($categories[0]->term_id);
                    for ($j = 0; $j < count($categories); $j++) {
                        $data[$i]['category_name_' . ($j + 1)] = $categories[$j]->name;
                        $data[$i]['category_link_' . ($j + 1)] = get_term_link($categories[$j]->term_id);
                    }
                } else {
                    $data[$i]['category_name'] = '';
                    $data[$i]['category_link'] = '';
                }
            }

            $post_meta = get_post_meta($post->ID);
            if (count($post_meta) && is_array($post_meta) && !empty($post_meta)) {
                foreach ($post_meta as $key => $value) {
                    if (count($value) && is_array($value) && !empty($value)) {
                        foreach ($value as $v) {
                            if (!empty($v) && !is_array($v) && !is_object($v)) {
                                $key = str_replace(array(
                                    '_',
                                    '-',
                                    ' '
                                ), array(
                                    '',
                                    '',
                                    ''
                                ), $key);
                                $key = $this->removeSpecChars($key);
                                $key = 'meta' . $key;
                                if (!isset($data[$i][$key])) {
                                    $data[$i][$key] = $v;
                                } else {
                                    $data[$i]['meta' . $key] = $v;
                                }
                            }
                        }
                    }
                }
            }

            if (!empty($dateVariables)) {
                foreach ($dateVariables as $dateVariable) {
                    if (isset($data[$i][$dateVariable])) {
                        $data[$i][$dateVariable . '_date'] = date_i18n(get_option('date_format'), strtotime($data[$i][$dateVariable]));
                        $data[$i][$dateVariable . '_time'] = date_i18n(get_option('time_format'), strtotime($data[$i][$dateVariable]));
                    }
                }
            }
        }

        if ($multi && !$this->data->get('slidecount', 1)) $data = array_slice($data, 0, $count);

        if ($multi) switch_to_blog($original_blog);

        return $data;
    }


    public function sortFunction($a, $b) {
        $order = $this->order[0];
        if (substr($order, 0, 1) == '_') {
            $order = substr($order, 1);
        }
        if ($this->order[1] == 'asc') {
            return strtotime($a->$order) - strtotime($b->$order);
        } else {
            return strtotime($b->$order) - strtotime($a->$order);
        }
    }

    public function removeSpecChars($str) {
        return preg_replace('/[^A-Za-z0-9\-]/', '', $str);
    }
}