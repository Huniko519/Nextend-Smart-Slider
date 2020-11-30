<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Translation\Translation;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Elements\WooCommerceCategories;

class WooCommerceCategory extends AbstractGenerator {

    protected $layout = 'article';

    private $categories;

    public function getDescription() {
        return n2_('Creates slides from your WooCommece categories. (Not from the products inside them.)');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');

        new WooCommerceCategories($filter, 'categories', n2_('Parent category'), 0);
        new Select($filter, 'level', n2_('Submenu limit'), 0, array(
            'options' => array(
                '0' => n2_('No limit'),
                '1' => 1,
                '2' => 2,
                '3' => 3,
                '4' => 4,
                '5' => 5
            )
        ));
    }

    protected function resetState() {
        $this->categories = array();
    }

    public function filterName($name) {
        return $name . Translation::getCurrentLocale() . get_woocommerce_currency();
    }

    public static function cacheKey($params) {
        return get_woocommerce_currency();
    }

    function getAllCategories($parent, $args, $level, $limit = 0) {
        $args['parent'] = $parent;

        $limit++;
        if ($level == 0 || $limit <= $level) {
            $cats = get_categories($args);
        } else {
            $cats = array();
        }

        foreach ($cats as $cat) {
            if ($cat->parent == $parent) {
                $this->categories[] = $cat;
                $this->getAllCategories($cat->cat_ID, $args, $level, $limit);
            }
        }
    }

    protected function _getData($count, $startIndex) {
        $mainCat = $this->data->get('categories', '0');
        $args    = array(
            'child_of'     => 0,
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'product_cat',
            'pad_counts'   => false
        );

        $level = $this->data->get('level', '0');

        $this->getAllCategories($mainCat, $args, $level);
        $this->categories = array_slice($this->categories, $startIndex, $count);
        $data             = array();
        foreach ($this->categories AS $category) {
            $image = wp_get_attachment_url(get_term_meta($category->term_id, 'thumbnail_id', true));
            if (!$image) $image = '';
            $r = array(
                'title'       => $category->name,
                'description' => $category->description,
                'image'       => $image,
                'count'       => $category->count,
                'url'         => get_category_link($category->term_id),
                'id'          => $category->term_id
            );

            $product_meta = get_term_meta($category->term_id);
            foreach ($product_meta as $meta => $value) {
                if (substr($meta, 0, 1) == '_') {
                    $meta = 'meta' . $meta;
                }
                $meta = str_replace('-', "_", $meta);
                if (is_serialized($value[0])) {
                    $product_attributes = unserialize($value[0]);
                    if (!empty($product_attributes) && is_array($product_attributes)) {
                        foreach ($product_attributes as $key => $value2) {
                            if (is_array($value2)) {
                                foreach ($value2 as $k => $v) {
                                    if (is_string($v)) {
                                        if (strpos($v, '|') !== false) {
                                            $helper = explode("|", $v);
                                            for ($fv = 0; $fv < count($helper); $fv++) {
                                                $r[$key . '_' . $k . '_' . $fv] = $helper[$fv];
                                            }
                                        } else {
                                            $r[$key . '_' . $k] = $v;
                                        }
                                    }
                                }
                            } else {
                                if (is_numeric($value2) || is_string($value2)) {
                                    if ($meta == 'dfiFeatured') {
                                        $parts = explode(',', $value2);
                                        if (isset($parts[1])) {
                                            $upload_dir = wp_upload_dir();
                                            $value2     = $upload_dir['baseurl'] . $parts[1];
                                        }
                                    }
                                    if (!array_key_exists($meta, $r)) {
                                        $r[$meta] = $value2;
                                    } else {
                                        $r['meta_' . $meta] = $value2;
                                    }
                                }
                            }
                        }
                    } else {
                        if (!array_key_exists($meta, $r)) {
                            $r[$meta] = '';
                        } else {
                            $r['meta_' . $meta] = '';
                        }
                    }
                } else {
                    if (is_array($value) && count($value) > 1) {
                        for ($fv = 0; $fv < count($value); $fv++) {
                            $v                    = $value[$fv];
                            $r[$meta . "_" . $fv] = $v;
                        }
                    } else {
                        if (!array_key_exists($meta, $r)) {
                            $r[$meta] = $value[0];
                        } else {
                            $r['meta_' . $meta] = $value[0];
                        }
                    }
                }

            }

            if (class_exists('acf')) {
                $fields = get_fields($category->taxonomy . '_' . $category->term_id);
                if (is_array($fields) && !empty($fields) && count($fields)) {
                    foreach ($fields AS $k => $v) {
                        $type = $this->getACFType($k, $category->ID);
                        $k    = str_replace('-', '', $k);

                        while (isset($r[$k])) {
                            $k = 'acf_' . $k;
                        }
                        if (!is_array($v) && !is_object($v)) {
                            if ($type['type'] == "image" && is_numeric($type["value"])) {
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $src            = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                $v              = $src[0];
                            }
                            $r[$k] = $v;
                        } else if (!is_object($v)) {
                            if (isset($v['url'])) {
                                $r[$k] = $v['url'];
                            } else if (is_array($v)) {
                                foreach ($v AS $v_v => $k_k) {
                                    if (is_array($k_k) && isset($k_k['url'])) {
                                        $r[$k . $v_v] = $k_k['url'];
                                    }
                                }
                            }
                        }
                        if ($type['type'] == "image" && (is_numeric($type["value"]) || is_array($type['value']))) {
                            if (is_array($type['value'])) {
                                $sizes = $this->getImageSizes($type["value"]["id"], $type["value"]["sizes"], $k);
                            } else {
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $sizes          = $this->getImageSizes($type["value"], $thumbnail_meta['sizes'], $k);
                            }
                            $r = array_merge($r, $sizes);
                        }
                    }
                }
            }
            $data[] = $r;
        }

        return $data;
    }

    protected function getImageSizes($thumbnail_id, $sizes, $prefix = false) {
        $data = array();
        if (!$prefix) {
            $prefix = "";
        } else {
            $prefix = $prefix . "_";
        }
        foreach ($sizes AS $size => $image) {
            $imageSrc                                               = wp_get_attachment_image_src($thumbnail_id, $size);
            $data[$prefix . 'image_' . $this->clearSizeName($size)] = $imageSrc[0];
        }

        return $data;
    }

    protected function clearSizeName($size) {
        return str_replace('-', '_', $size);
    }

    protected function getACFType($key, $post_id) {
        $type = get_field_object($key, $post_id);

        return $type;
    }

}
