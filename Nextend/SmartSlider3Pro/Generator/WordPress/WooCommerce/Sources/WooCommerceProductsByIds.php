<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Translation\Translation;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use WC_Product_Factory;

class WooCommerceProductsByIds extends AbstractGenerator {

    protected $layout = 'product';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'WooCommerce' . n2_('Products by IDs and/or SKUs'));
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');
        new Textarea($filter, 'ids', n2_('IDs (one per line)'), '', array(
            'width'  => 200,
            'height' => 200
        ));
        new Textarea($filter, 'skus', n2_('SKUs (one per line)'), '', array(
            'width'  => 200,
            'height' => 200
        ));
    }

    public function filterName($name) {
        return $name . Translation::getCurrentLocale() . get_woocommerce_currency();
    }

    public static function cacheKey($params) {
        return get_woocommerce_currency();
    }

    protected function _getData($count, $startIndex) {
        $productFactory = new WC_Product_Factory();
        $i              = 0;
        $data           = array();

        $ids  = $this->getIDs();
        $skus = $this->getSKUs();
        if (!empty($skus)) {
            foreach ($skus as $sku) {
                $ids[] = wc_get_product_id_by_sku(trim($sku));
            }
        }

        foreach ($ids as $id) {
            $product = $productFactory->get_product($id);
            if ($product && $product->is_visible()) {
                $product_id   = $product->get_id();
                $thumbnail_id = get_post_thumbnail_id($product_id);
                $image        = wp_get_attachment_url($thumbnail_id);
                $thumbnail    = wp_get_attachment_image_src($thumbnail_id);
                if ($thumbnail[0] != null) {
                    $thumbnail = $thumbnail[0];
                } else {
                    $thumbnail = $image;
                }

                $data[$i] = array(
                    'title'          => $product->get_title(),
                    'url'            => $product->get_permalink(),
                    'description'    => get_post($product_id)->post_content,
                    'image'          => ResourceTranslator::urlToResource($image),
                    'thumbnail'      => ResourceTranslator::urlToResource($thumbnail),
                    'price'          => wc_price($product->get_price()),
                    'price_html'     => $product->get_price_html(),
                    'regular_price'  => wc_price($product->get_regular_price()),
                    'price_with_tax' => wc_price(wc_get_price_including_tax($product)),
                    'rating'         => $product->get_average_rating()
                );

                $image_sizes = get_intermediate_image_sizes();
                foreach ($image_sizes as $image_size) {
                    $image_data = wp_get_attachment_image_src($thumbnail_id, $image_size);
                    $data[$i]   += array(
                        'image_' . $image_size => ResourceTranslator::urlToResource($image_data[0])
                    );
                }

                $product_gallery = get_post_meta($product_id, "_product_image_gallery", true);
                if (!empty($product_gallery)) {
                    $product_gallery = explode(',', $product_gallery);
                    for ($fora = 0; $fora < count($product_gallery); $fora++) {
                        $data[$i]['product_gallery_' . $fora . '_image']     = wp_get_attachment_url($product_gallery[$fora]);
                        $data[$i]['product_gallery_' . $fora . '_thumbnail'] = wp_get_attachment_image_src($product_gallery[$fora])[0];
                        if (empty($data[$i]['product_gallery_' . $fora . '_thumbnail'])) {
                            $data[$i]['product_gallery_' . $fora . '_thumbnail'] = $data[$i]['product_gallery_' . $fora . '_image'];
                        }
                    }
                }

                $product_type = $product->get_type();
                if ($product->has_child() && $product_type != "grouped") {
                    $data[$i] += array(
                        'variation_min_price'                  => $product->get_variation_regular_price('min'),
                        'variation_min_price_currency'         => wc_price($product->get_variation_regular_price('min')),
                        'variation_min_regular_price'          => $product->get_variation_sale_price('min'),
                        'variation_min_regular_price_currency' => wc_price($product->get_variation_sale_price('min')),
                        'variation_min_sale_price'             => $product->get_variation_price('min'),
                        'variation_min_sale_price_currency'    => wc_price($product->get_variation_price('min')),
                        'variation_max_price'                  => $product->get_variation_regular_price('max'),
                        'variation_max_price_currency'         => wc_price($product->get_variation_regular_price('max')),
                        'variation_max_regular_price'          => $product->get_variation_sale_price('max'),
                        'variation_max_regular_price_currency' => wc_price($product->get_variation_sale_price('max')),
                        'variation_max_sale_price'             => $product->get_variation_price('max'),
                        'variation_max_sale_price_currency'    => wc_price($product->get_variation_price('max'))
                    );
                }

                $product_meta = get_post_meta($product_id);
                foreach ($product_meta as $meta => $value) {
                    $meta = str_replace('-', '', $meta);
                    if (substr($meta, 0, 1) == '_') {
                        $meta = 'meta' . $meta;
                    }
                    if (is_serialized($value[0])) {
                        $product_attributes = unserialize($value[0]);
                        if (!empty($product_attributes) && is_array($product_attributes)) {
                            foreach ($product_attributes as $key => $value2) {
                                $key = str_replace('-', '', $key);
                                if (is_array($value2)) {
                                    foreach ($value2 as $k => $v) {
                                        $k = str_replace('-', '', $k);
                                        if (is_string($v)) {
                                            if (strpos($v, '|') !== false) {
                                                $helper = explode("|", $v);
                                                for ($fv = 0; $fv < count($helper); $fv++) {
                                                    $fv                                    = str_replace('-', '', $fv);
                                                    $data[$i][$key . '_' . $k . '_' . $fv] = $helper[$fv];
                                                }
                                            } else {
                                                $data[$i][$key . '_' . $k] = $v;
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
                                        if (!array_key_exists($meta, $data[$i])) {
                                            $data[$i][$meta] = $value2;
                                        } else {
                                            $data[$i]['meta_' . $meta] = $value2;
                                        }
                                    }
                                }
                            }
                        } else {
                            if (!array_key_exists($meta, $data[$i])) {
                                $data[$i][$meta] = '';
                            } else {
                                $data[$i]['meta_' . $meta] = '';
                            }
                        }
                    } else {
                        if (is_array($value) && count($value) > 1) {
                            for ($fv = 0; $fv < count($value); $fv++) {
                                $v                           = $value[$fv];
                                $data[$i][$meta . "_" . $fv] = $v;
                            }
                        } else {
                            if (!array_key_exists($meta, $data[$i])) {
                                $data[$i][$meta] = $value[0];
                            } else {
                                $data[$i]['meta_' . $meta] = $value[0];
                            }
                        }
                    }

                }

                if (class_exists('acf')) {
                    $fields = get_fields($product_id);
                    if (is_array($fields) && count($fields) && !empty($fields)) {
                        foreach ($fields as $k => $v) {
                            $type = $this->getACFType($k, $product_id);
                            $k    = str_replace('-', '', $k);

                            while (isset($data[$i][$k])) {
                                $k = 'acf_' . $k;
                            }

                            if (!is_array($v) && !is_object($v)) {
                                if ($type['type'] == "image" && is_numeric($type["value"])) {
                                    $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                    $src            = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                    $v              = $src[0];
                                }
                                $data[$i][$k] = $v;
                            } else if (!is_object($v)) {
                                if (isset($v['url'])) {
                                    $data[$i][$k] = $v['url'];
                                } else if (is_array($v)) {
                                    foreach ($v as $v_v => $k_k) {
                                        if (is_array($k_k) && isset($k_k['url'])) {
                                            $data[$i][$k . $v_v] = $k_k['url'];
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
                                $data[$i] = array_merge($data[$i], $sizes);
                            }
                        }
                    }
                }

                $post   = get_post($id);
                $seller = get_user_by("id", $post->post_author);
                if (is_object($seller)) {
                    $data[$i]['seller_display_name']  = $seller->display_name;
                    $data[$i]['seller_user_nicename'] = $seller->user_nicename;
                }

                if ($product->is_on_sale()) {
                    $data[$i]['sale_price'] = wc_price($product->get_sale_price());
                } else {
                    $data[$i]['sale_price'] = $data[$i]['price'];
                }

                $data[$i]['ID'] = $product_id;

                $i++;
            }
        }

        return $data;
    }

    protected function getSKUs($field = 'skus') {

        $skus = $this->data->get($field);
        if (empty($skus)) {
            return false;
        }

        return array_map('strval', explode("\n", str_replace(array(
            "\r\n",
            "\n\r",
            "\r"
        ), "\n", $this->data->get($field))));
    }

    protected function getImageSizes($thumbnail_id, $sizes, $prefix = false) {
        $data = array();
        if (!$prefix) {
            $prefix = "";
        } else {
            $prefix = $prefix . "_";
        }
        foreach ($sizes as $size => $image) {
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
