<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\BestWebSoft\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\BestWebSoft\Elements\BestWebSoftGalleries;

class BestWebSoftGallery extends AbstractGenerator {

    protected $layout = 'image_extended';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'BestWebSoft Gallery');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');

        new BestWebSoftGalleries($filter, 'gallery', n2_('Gallery'));

    }

    protected function _getData($count, $startIndex) {
        $data = array();
        global $gllr_options;

        $galleryID = $this->data->get('gallery', '');
        $imagesIDs = explode(',', get_post_meta($galleryID, '_gallery_images', true));
        $posts     = get_posts(array(
            "showposts"          => -1,
            "what_to_show"       => "posts",
            "post_status"        => "inherit",
            "post_type"          => "attachment",
            "orderby"            => $gllr_options['order_by'],
            "order"              => $gllr_options['order'],
            "post_mime_type"     => "image/jpeg,image/gif,image/jpg,image/png",
            "post__in"           => array_slice($imagesIDs, $startIndex, $count),
            "meta_key"           => '_gallery_order_' . $galleryID,
            "ignore_custom_sort" => true
        ));

        $i = 0;
        foreach ($posts as $p) {
            $meta = get_post_meta($p->ID);

            $src               = wp_get_attachment_image_src($p->ID, "full");
            $data[$i]['image'] = ResourceTranslator::urlToResource($src[0]);
            $thumbnail         = wp_get_attachment_image_src($p->ID, "thumbnail");
            if ($thumbnail[0] != null) {
                $data[$i]['thumbnail'] = ResourceTranslator::urlToResource($thumbnail[0]);
            } else {
                $data[$i]['thumbnail'] = $data['image'];
            }

            if (array_key_exists('gllr_image_text', $meta)) {
                $data[$i]['title'] = $meta['gllr_image_text'][0];
            } else {
                $data[$i]['title'] = $p->post_title;
            }

            if (array_key_exists('gllr_image_alt_tag', $meta)) {
                $data[$i]['description'] = $meta['gllr_image_alt_tag'][0];
            }
            $data[$i]['url']       = get_permalink($p->post_parent);
            $data[$i]['url_label'] = n2_('View gallery');

            if (array_key_exists('gllr_link_url', $meta)) {
                $data[$i]['external_url'] = $meta['gllr_link_url'][0];
            } else {
                $data[$i]['external_url'] = '#';
            }

            $data[$i]['comment_count'] = $p->comment_count;
            $data[$i]['ID']            = $p->ID;

            $i++;
        }

        return $data;
    }

}
