<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Filter;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Elements\TheEventsCalendarCategories;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Elements\TheEventsCalendarMetaKeys;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Elements\TheEventsCalendarOrganizers;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Elements\TheEventsCalendarTags;
use Nextend\SmartSlider3Pro\Generator\WordPress\TheEventsCalendar\Elements\TheEventsCalendarVenues;

class TheEventsCalendarEvents extends AbstractGenerator {

    protected $layout = 'event';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s events.'), 'The Events Calendar');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');

        new TheEventsCalendarCategories($filter, 'categories', n2_('Category'), 0, array(
            'isMultiple' => true
        ));
        new TheEventsCalendarTags($filter, 'tags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));
        new TheEventsCalendarOrganizers($filter, 'organizers', n2_('Organizers'), 0, array(
            'isMultiple' => true
        ));
        new TheEventsCalendarVenues($filter, 'venues', n2_('Venues'), 0, array(
            'isMultiple' => true
        ));

        $limit = $filterGroup->createRow('limit');
        new Filter($limit, 'started', n2_('Started'), 0);
        new Filter($limit, 'ended', n2_('Ended'), -1);
        new Filter($limit, 'featured', n2_('Featured'), 0);
        new Filter($limit, 'hide', n2_('Hide From Event Listings'), 0);

        $metaGroup = $filterGroup->createRow('meta');
        new TheEventsCalendarMetaKeys($metaGroup, 'metakey', n2_('Field name'), 0);
        new Select($metaGroup, 'metacompare', n2_('Compare method'), '=', array(
            'options' => array(
                '='           => '=',
                '!='          => '!=',
                '>'           => '>',
                '>='          => '>=',
                '<'           => '<',
                '<='          => '<=',
                'LIKE'        => 'LIKE',
                'NOT LIKE'    => 'NOT LIKE',
                'IN'          => 'IN',
                'NOT IN'      => 'NOT IN',
                'BETWEEN'     => 'BETWEEN',
                'NOT BETWEEN' => 'NOT BETWEEN',
                'REGEXP'      => 'REGEXP',
                'NOT REGEXP'  => 'NOT REGEXP',
                'RLIKE'       => 'RLIKE',
                'EXISTS'      => 'EXISTS',
                'NOT EXISTS'  => 'NOT EXISTS'
            )
        ));
        new Text($metaGroup, 'metavalue', n2_('Field value'));

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order');
        new GeneratorOrder($order, 'order', '_EventStartDate|*|asc', array(
            'options' => array(
                'default'         => n2_('Default'),
                '_EventStartDate' => n2_('Event start date'),
                '_EventEndDate'   => n2_('Event end date'),
                '_EventCost'      => n2_('Event cost'),
                'post_date'       => n2_('Event creation date'),
                'post_modified'   => n2_('Event modification date'),
                'title'           => n2_('Title')
            )
        ));

        $metaOrder = $orderGroup->createRow('meta-order');

        new TheEventsCalendarMetaKeys($metaOrder, 'meta_order_key', n2_('Metakey name'), 0, array(
            'tipLabel'       => n2_('Metakey name'),
            'tipDescription' => n2_('If its set, this will be used instead of the \'Field\' value.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1895-wordpress-the-events-calendar-generator#metakey-name'
        ));

        new Radio($metaOrder, 'meta_orderby', n2_('Sorting method'), 'meta_value_num', array(
            'options' => array(
                'meta_value_num' => n2_('Numeric'),
                'meta_value'     => n2_('Alphabetic')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        $tax_query  = array();
        $meta_query = array();

        $categories = explode('||', $this->data->get('categories', 0));
        if (!in_array(0, $categories)) {
            $tax_query[] = array(
                'taxonomy' => 'tribe_events_cat',
                'field'    => 'term_id',
                'terms'    => $categories
            );
        }

        $tags = explode('||', $this->data->get('tags', 0));
        if (!in_array(0, $tags)) {
            $tax_query[] = array(
                'taxonomy' => 'post_tag',
                'field'    => 'term_id',
                'terms'    => $tags
            );
        }

        $organizers = explode('||', $this->data->get('organizers', 0));
        if (!in_array(0, $organizers)) {
            if (count($organizers)) {
                $meta_query[] = array(
                    'key'   => '_EventOrganizerID',
                    'value' => $organizers
                );
            }
        }

        $venues = explode('||', $this->data->get('venues', 0));
        if (!in_array(0, $venues)) {
            if (count($venues)) {
                $meta_query[] = array(
                    'key'   => '_EventVenueID',
                    'value' => $venues
                );
            }
        }

        switch ($this->data->get('featured', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'   => '_tribe_featured',
                    'value' => '1'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_tribe_featured',
                    'compare' => 'NOT EXISTS'
                );
                break;
        }

        switch ($this->data->get('hide', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'   => '_EventHideFromUpcoming',
                    'value' => 'yes'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_EventHideFromUpcoming',
                    'compare' => 'NOT EXISTS'
                );
                break;
        }

        $today = current_time('mysql');

        switch ($this->data->get('started', '0')) {
            case 1:
                $meta_query[] = array(
                    'key'     => '_EventStartDate',
                    'value'   => $today,
                    'type'    => 'DATETIME',
                    'compare' => '<'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_EventStartDate',
                    'value'   => $today,
                    'type'    => 'DATETIME',
                    'compare' => '>='
                );
                break;
        }

        switch ($this->data->get('ended', '-1')) {
            case 1:
                $meta_query[] = array(
                    'key'     => '_EventEndDate',
                    'value'   => $today,
                    'type'    => 'DATETIME',
                    'compare' => '<'
                );
                break;
            case -1:
                $meta_query[] = array(
                    'key'     => '_EventEndDate',
                    'value'   => $today,
                    'type'    => 'DATETIME',
                    'compare' => '>='
                );
                break;
        }

        $metaKey = $this->data->get('metakey', '0');
        if (!empty($metaKey)) {
            $metaValue    = $this->data->get('metavalue', '');
            $compare      = $this->data->get('metacompare', '');
            $meta_query[] = array(
                'key'     => $metaKey,
                'value'   => $metaValue,
                'compare' => $compare
            );
        }

        list($orderBy, $order) = Common::parse($this->data->get('order', '_EventStartDate|*|asc'));

        $meta_order_key = $this->data->get('meta_order_key');
        $meta_key       = '';
        if (!empty($meta_order_key)) {
            $orderBy  = $this->data->get('meta_orderby', 'meta_value_num');
            $meta_key = $meta_order_key;
        }

        $args = array(
            'offset'           => $startIndex,
            'posts_per_page'   => $count,
            'meta_key'         => $meta_key,
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'post_type'        => 'tribe_events',
            'tax_query'        => $tax_query,
            'meta_query'       => $meta_query
        );

        if ($orderBy != 'none') {
            $args += array(
                'orderby'            => $orderBy,
                'ignore_custom_sort' => true
            );
        }

        if ($orderBy != 'default') {
            $args += array(
                'ignore_custom_sort' => true
            );
            if ($orderBy[0] == '_') {
                $args['orderby']  = 'meta_value'; //meta_value = strval, meta_value_num = intval
                $args['meta_key'] = $orderBy;
            } else {
                $args['orderby'] = $orderBy;
            }
        }

        $args['order'] = $order;

        $posts_array = get_posts($args);

        //need a one level array, because of ordering with group result
        $data = array();

        for ($i = 0; $i < count($posts_array); $i++) {
            $post_meta         = get_post_meta($posts_array[$i]->ID);
            $data[$i]['title'] = $posts_array[$i]->post_title;
            if (isset($post_meta['wps_subtitle'][0])) {
                $data[$i]['subtitle'] = $post_meta['wps_subtitle'][0];
            }
            $data[$i]['description'] = $data[$i]['excerpt'] = $posts_array[$i]->post_content;
            if (!empty($posts_array[$i]->post_excerpt)) {
                $data[$i]['excerpt'] = $posts_array[$i]->post_excerpt;
            }
            $data[$i]['image'] = ResourceTranslator::urlToResource(wp_get_attachment_url(get_post_thumbnail_id($posts_array[$i]->ID)));
            $thumbnail         = wp_get_attachment_image_src(get_post_thumbnail_id($posts_array[$i]->ID, 'thumbnail'));
            if ($thumbnail[0]) {
                $data[$i]['thumbnail'] = ResourceTranslator::urlToResource($thumbnail[0]);
            } else if (!empty($data['image'])) {
                $data[$i]['thumbnail'] = $data['image'];
            }
            $data[$i]['image_alt'] = get_post_meta(get_post_thumbnail_id($posts_array[$i]->ID), '_wp_attachment_image_alt', true);
            $data[$i]['url']       = get_permalink($posts_array[$i]->ID);

            $start                  = strtotime($post_meta['_EventStartDate'][0]);
            $data[$i]['start_date'] = date_i18n(get_option('date_format'), $start);
            $data[$i]['start_time'] = date_i18n(get_option('time_format'), $start);

            $end                  = strtotime($post_meta['_EventEndDate'][0]);
            $data[$i]['end_date'] = date_i18n(get_option('date_format'), $end);
            $data[$i]['end_time'] = date_i18n(get_option('time_format'), $end);

            $event_cats_args = array(
                'orderby' => 'name',
                'order'   => 'ASC',
                'fields'  => 'all'
            );
            $category        = wp_get_object_terms($posts_array[$i]->ID, array('tribe_events_cat'), $event_cats_args);
            $j               = 0;
            if (is_array($category) && count($category) > 1) {
                foreach ($category AS $cat) {
                    $data[$i]['category_name_' . $j] = $cat->name;
                    $j++;
                }
            } else if (!empty($category)) {
                $data[$i]['category_name_0'] = $category[0]->name;
            }

            $data[$i]['ID'] = $posts_array[$i]->ID;

            $data[$i]['EventCurrencySymbol'] = $post_meta['_EventCurrencySymbol'][0];
            $data[$i]['EventCost']           = $post_meta['_EventCost'][0];
            $data[$i]['EventURL']            = $post_meta['_EventURL'][0];

            //venue
            $venue_post_meta = isset($post_meta['_EventVenueID'][0]) ? get_post_meta($post_meta['_EventVenueID'][0]) : '';

            $data[$i]['VenueName']     = isset($post_meta['_EventVenueID'][0]) ? get_the_title($post_meta['_EventVenueID'][0]) : '';
            $data[$i]['VenueAddress']  = isset($venue_post_meta['_VenueAddress'][0]) ? $venue_post_meta['_VenueAddress'][0] : '';
            $data[$i]['VenueCity']     = isset($venue_post_meta['_VenueCity'][0]) ? $venue_post_meta['_VenueCity'][0] : '';
            $data[$i]['VenueCountry']  = isset($venue_post_meta['_VenueCountry'][0]) ? $venue_post_meta['_VenueCountry'][0] : '';
            $data[$i]['VenueProvince'] = isset($venue_post_meta['_VenueProvince'][0]) ? $venue_post_meta['_VenueProvince'][0] : '';
            $data[$i]['VenueState']    = isset($venue_post_meta['_VenueState'][0]) ? $venue_post_meta['_VenueState'][0] : '';
            $data[$i]['VenueZip']      = isset($venue_post_meta['_VenueZip'][0]) ? $venue_post_meta['_VenueZip'][0] : '';
            $data[$i]['VenuePhone']    = isset($venue_post_meta['_VenuePhone'][0]) ? $venue_post_meta['_VenuePhone'][0] : '';
            $data[$i]['VenueURL']      = isset($venue_post_meta['_VenueURL'][0]) ? $venue_post_meta['_VenueURL'][0] : '';
            $data[$i]['VenueImage']    = isset($venue_post_meta['_thumbnail_id'][0]) ? ResourceTranslator::urlToResource(wp_get_attachment_url($venue_post_meta['_thumbnail_id'][0])) : '';

            //organizer
            $organizer_post_meta          = isset($post_meta['_EventVenueID'][0]) ? get_post_meta($post_meta['_EventOrganizerID'][0]) : '';
            $data[$i]['OrganizerName']    = isset($post_meta['_EventVenueID'][0]) ? get_the_title($post_meta['_EventOrganizerID'][0]) : '';
            $data[$i]['OrganizerPhone']   = isset($organizer_post_meta['_OrganizerPhone'][0]) ? $organizer_post_meta['_OrganizerPhone'][0] : '';
            $data[$i]['OrganizerWebsite'] = isset($organizer_post_meta['_OrganizerWebsite'][0]) ? $organizer_post_meta['_OrganizerWebsite'][0] : '';
            $data[$i]['OrganizerEmail']   = isset($organizer_post_meta['_OrganizerEmail'][0]) ? $organizer_post_meta['_OrganizerEmail'][0] : '';
            $data[$i]['OrganizerImage']   = isset($organizer_post_meta['_thumbnail_id'][0]) ? ResourceTranslator::urlToResource(wp_get_attachment_url($organizer_post_meta['_thumbnail_id'][0])) : '';

        }

        return $data;
    }

}