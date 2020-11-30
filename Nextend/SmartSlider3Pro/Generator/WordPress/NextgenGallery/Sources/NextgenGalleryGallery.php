<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\NextgenGallery\Sources;

use C_Component_Registry;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\NextgenGallery\Elements\NextgenGalleries;
use nggGallery;

class NextgenGalleryGallery extends AbstractGenerator {

    protected $layout = 'image';
    protected $legacy = false;
    protected $storage;

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s galleries.'), 'NextGEN Gallery');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');
        new NextgenGalleries($filter, 'gallery', n2_('Source gallery'), '');
    }

    private function imageUrl($image, $imageOrThumb = 1) {
        if ($imageOrThumb) {
            $function = 'get_image_url';
        } else {
            $function = 'get_thumbnail_url';
        }

        if ($this->legacy) {
            $imageUrl = nggGallery::$function($image->pid, $image->path, $image->filename);
        } else {
            $imageUrl = $this->storage->$function($image);
        }

        return ResourceTranslator::urlToResource($imageUrl);
    }

    protected function _getData($count, $startIndex) {
        if (class_exists('nggGallery') && !class_exists('C_Component_Registry')) {
            $this->legacy = true;
        } else {
            $this->storage = C_Component_Registry::get_instance()
                                                 ->get_utility('I_Gallery_Storage');
        }

        global $wpdb;

        $data = array();

        $images = $wpdb->get_results("SELECT a.*, b.path FROM " . $wpdb->base_prefix . "ngg_pictures AS a LEFT JOIN " . $wpdb->base_prefix . "ngg_gallery AS b ON a.galleryid = b.gid WHERE a.galleryid = '" . intval($this->data->get('gallery', 0)) . "' ORDER BY a.sortorder LIMIT " . $startIndex . ", " . $count);

        $i = 0;
        foreach ($images as $image) {
            $data[$i]['image']       = $this->imageUrl($image);
            $data[$i]['thumbnail']   = $this->imageUrl($image, 0);
            $data[$i]['title']       = $image->alttext;
            $data[$i]['description'] = $image->description;
            $data[$i]['id']          = $image->pid;

            $i++;
        }

        return $data;
    }
}