<?php

namespace Nextend\SmartSlider3\Generator\WordPress\Posts\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class PostsPostsByIDs extends AbstractGenerator {

    protected $layout = 'article';

    public function getDescription() {
        return n2_('Creates slides from the posts with the set IDs.');
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');
        new Textarea($filter, 'ids', n2_('Post or Page IDs'), '', array(
            'width'          => 280,
            'height'         => 160,
            'tipLabel'       => n2_('Post or Page IDs'),
            'tipDescription' => sprintf(n2_('You can write the ID of the page you want to show in your generator. %1$s Write one ID per line.'), '<br>')
        ));
    }

    protected function _getData($count, $startIndex) {
        global $post, $wp_query;
        $tmpPost = $post;

        if (has_filter('the_content', 'siteorigin_panels_filter_content')) {
            $siteorigin_panels_filter_content = true;
            remove_filter('the_content', 'siteorigin_panels_filter_content');
        } else {
            $siteorigin_panels_filter_content = false;
        }

        $i    = 0;
        $data = array();

        foreach ($this->getIDs() AS $id) {
            $record = array();
            $post   = get_post($id);
            if (!$post) continue;
            setup_postdata($post);
            $wp_query->post = $post;

            $record['id'] = $post->ID;


            $record['url']           = get_permalink();
            $record['title']         = apply_filters('the_title', get_the_title(), $post->ID);
            $record['description']   = $record['content'] = get_the_content();
            $record['author_name']   = $record['author'] = get_the_author();
            $userID                  = get_the_author_meta('ID');
            $record['author_url']    = get_author_posts_url($userID);
            $record['author_avatar'] = get_avatar_url($userID);
            $record['date']          = get_the_date();
            $record['modified']      = get_the_modified_date();

            $category = get_the_category($post->ID);
            if (isset($category[0])) {
                $record['category_name'] = $category[0]->name;
                $record['category_link'] = get_category_link($category[0]->cat_ID);
            } else {
                $record['category_name'] = '';
                $record['category_link'] = '';
            }

            $thumbnail_id             = get_post_thumbnail_id($post->ID);
            $record['featured_image'] = wp_get_attachment_image_url($thumbnail_id, 'full');
            if (!$record['featured_image']) {
                $record['featured_image'] = '';
            } else {
                $thumbnail_meta = get_post_meta($thumbnail_id, '_wp_attachment_metadata', true);
                if (isset($thumbnail_meta['sizes'])) {
                    $sizes  = $this->getImageSizes($thumbnail_id, $thumbnail_meta['sizes']);
                    $record = array_merge($record, $sizes);
                }
                $record['alt'] = '';
                $alt           = get_post_meta($thumbnail_id, '_wp_attachment_image_alt', true);
                if (isset($alt)) {
                    $record['alt'] = $alt;
                }
            }

            $record['thumbnail'] = $record['image'] = $record['featured_image'];
            $record['url_label'] = 'View post';

            if (class_exists('acf')) {
                $fields = get_fields($post->ID);
                if (is_array($fields) && !empty($fields) && count($fields)) {
                    foreach ($fields AS $k => $v) {
                        $type = $this->getACFType($k, $post->ID);
                        $k    = str_replace('-', '', $k);

                        while (isset($record[$k])) {
                            $k = 'acf_' . $k;
                        }
                        if (!is_array($v) && !is_object($v)) {
                            if ($type['type'] == "image" && is_numeric($type["value"])) {
                                $thumbnail_meta = wp_get_attachment_metadata($type["value"]);
                                $src            = wp_get_attachment_image_src($v, $thumbnail_meta['file']);
                                $v              = $src[0];
                            }
                            $record[$k] = $v;
                        } else if (!is_object($v)) {
                            if (isset($v['url'])) {
                                $record[$k] = $v['url'];
                            } else if (is_array($v)) {
                                foreach ($v AS $v_v => $k_k) {
                                    if (is_array($k_k) && isset($k_k['url'])) {
                                        $record[$k . $v_v] = $k_k['url'];
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
                            $record = array_merge($record, $sizes);
                        }
                    }
                }
            }
            if (isset($record['primarytermcategory'])) {
                $primary                         = get_category($record['primarytermcategory']);
                $record['primary_category_name'] = $primary->name;
                $record['primary_category_link'] = get_category_link($primary->cat_ID);
            }
            $record['excerpt'] = get_the_excerpt();

            $record = apply_filters('smartslider3_posts_postsbyids_data', $record);

            $data[$i] = &$record;
            unset($record);
            $i++;
        }
        if ($siteorigin_panels_filter_content) {
            add_filter('the_content', 'siteorigin_panels_filter_content');
        }

        $wp_query->post = $tmpPost;
        wp_reset_postdata();

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
        return preg_replace("/-/", "_", $size);
    }

    protected function getACFType($key, $post_id) {
        $type = get_field_object($key, $post_id);

        return $type;
    }
}