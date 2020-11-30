<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Elements\WebdoradoPhotoGalleryGalleries;
use Nextend\SmartSlider3Pro\Generator\WordPress\WebdoradoPhotoGallery\Elements\WebdoradoPhotoGalleryTags;

class WebdoradoPhotoGalleryImages extends AbstractGenerator {

    protected $layout = 'image';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'Photo Gallery by 10Web');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');

        new WebdoradoPhotoGalleryGalleries($filter, 'webdoradogalleries', n2_('Galleries'), 0, array(
            'isMultiple' => true
        ));
        new WebdoradoPhotoGalleryTags($filter, 'webdoradotags', n2_('Tags'), 0, array(
            'isMultiple' => true
        ));

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order-row');
        new GeneratorOrder($order, 'order', 'a.date|*|desc', array(
            'options' => array(
                '0'               => n2_('None'),
                'a.id'            => 'ID',
                'a.filename'      => n2_('Filename'),
                'a.alt'           => n2_('Alt tag'),
                'a.date'          => n2_('Upload date'),
                'a.order'         => n2_('Order'),
                'a.comment_count' => n2_('Comment count'),
                'a.avg_rating'    => n2_('Average rating'),
                'a.rate_count'    => n2_('Rate count'),
                'a.hit_count'     => n2_('Hit count')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        global $wpdb;

        $where = array('a.published = 1');

        $galleries = array_map('intval', explode('||', $this->data->get('webdoradogalleries', '')));
        if (!in_array('0', $galleries)) {
            $where[] = "a.gallery_id IN (" . implode(',', $galleries) . ")";
        }

        $tags = array_map('intval', explode('||', $this->data->get('webdoradotags', '')));
        if (!in_array('0', $tags)) {
            $where[] = "a.id IN (SELECT image_id FROM " . $wpdb->base_prefix . "bwg_image_tag WHERE tag_id IN (" . implode(',', $tags) . "))";
        }

        list($orderBy, $sort) = Common::parse($this->data->get('order', 'a.date|*|desc'));

        $pictures = $wpdb->get_results("SELECT
                                        a.slug AS slug,
                                        a.filename AS filename,
                                        a.image_url AS image_url,
                                        a.thumb_url AS thumb_url,
                                        a.description AS description,
                                        a.alt AS alt, a.date AS date,
                                        a.author AS author,
                                        a.comment_count AS comment_count,
                                        a.avg_rating AS avg_rating,
                                        a.rate_count AS rate_count,
                                        a.hit_count AS hit_count,
                                        a.redirect_url AS redirect_url,
                                        a.filetype AS filetype,
                                        b.name AS gallery_name,
                                        b.slug AS gallery_slug,
                                        b.description AS gallery_description,
                                        b.page_link AS gallery_page_link,
                                        b.preview_image AS gallery_preview_image,
                                        b.random_preview_image AS random_preview_image
                                        FROM " . $wpdb->base_prefix . "bwg_image AS a
                                        LEFT JOIN " . $wpdb->base_prefix . "bwg_gallery AS b
                                        ON a.gallery_id = b.id
                                        WHERE " . implode(' AND ', $where) . "
                                        " . ($orderBy != '0' ? "ORDER BY " . $orderBy . ' ' . $sort : "") . "
                                        LIMIT " . $startIndex . ", " . $count);

        $siteUrl = get_site_url();

        $data = array();
        foreach ($pictures as $p) {
            if (strpos($p->filetype, 'EMBED_OEMBED') !== false) {
                //youtube, vimeo, etc. types
                $r = array(
                    'file_url'  => $p->image_url,
                    'image'     => $p->thumb_url,
                    'thumbnail' => $p->thumb_url
                );
            } else {
                //image types
                $r = array(
                    'image'     => ResourceTranslator::urlToResource($siteUrl . "/wp-content/uploads/photo-gallery" . $p->image_url),
                    'thumbnail' => ResourceTranslator::urlToResource($siteUrl . "/wp-content/uploads/photo-gallery" . $p->thumb_url)
                );
            }

            $r += array(
                'title'                 => $p->alt,
                'slug'                  => $p->slug,
                'filename'              => $p->filename,
                'date'                  => $p->date,
                'author'                => get_the_author_meta('display_name', $p->author),
                'comment_count'         => $p->comment_count,
                'average_rating'        => $p->avg_rating,
                'rate_count'            => $p->rate_count,
                'hit_count'             => $p->hit_count,
                'redirect_url'          => $p->redirect_url,
                'gallery_name'          => $p->gallery_name,
                'gallery_slug'          => $p->gallery_slug,
                'gallery_description'   => $p->gallery_description,
                'gallery_page_link'     => $p->gallery_page_link,
                'gallery_preview_image' => ResourceTranslator::urlToResource($siteUrl . "/wp-content/uploads/photo-gallery" . $p->gallery_preview_image)
            );

            if (strpos($p->random_preview_image, 'http:') !== false && strpos($p->random_preview_image, 'https:') !== false) {
                $r += array(
                    'gallery_random_preview_image' => ResourceTranslator::urlToResource($siteUrl . "/wp-content/uploads/photo-gallery" . $p->random_preview_image)
                );
            } else {
                $r += array(
                    'gallery_random_preview_image' => $p->random_preview_image
                );
            }

            $data[] = $r;
        }

        return $data;
    }

}
