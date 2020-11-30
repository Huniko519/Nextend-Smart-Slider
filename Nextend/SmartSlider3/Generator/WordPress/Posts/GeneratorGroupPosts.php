<?php

namespace Nextend\SmartSlider3\Generator\WordPress\Posts;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3\Generator\WordPress\Posts\Sources\PostsAllCustomPosts;
use Nextend\SmartSlider3\Generator\WordPress\Posts\Sources\PostsCustomPosts;
use Nextend\SmartSlider3\Generator\WordPress\Posts\Sources\PostsPosts;
use Nextend\SmartSlider3\Generator\WordPress\Posts\Sources\PostsPostsByIDs;

class GeneratorGroupPosts extends AbstractGeneratorGroup {

    protected $name = 'posts';

    public function getLabel() {
        return n2_('Posts');
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'WordPress posts');
    }

    protected function loadSources() {

        new PostsPosts($this, 'posts', n2_('Posts by filter'));

        new PostsPostsByIDs($this, 'postsbyids', n2_('Posts by IDs'));
        $customPosts = get_post_types();

        unset($customPosts['nav_menu_item'], $customPosts['revision'], $customPosts['attachment']);

        foreach ($customPosts AS $post_type) {
            $post_type_object = get_post_type_object($post_type);
            if ($post_type_object->public) {

                new PostsCustomPosts($this, 'customposts__' . $post_type, $post_type, n2_('Custom') . ' - ' . @get_post_type_object($post_type)->labels->name . ' (' . $post_type . ')');
            }
        }
        new PostsAllCustomPosts($this, 'allcustomposts', n2_('All custom posts'));
    
    }
}