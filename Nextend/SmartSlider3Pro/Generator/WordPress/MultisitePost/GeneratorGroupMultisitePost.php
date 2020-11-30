<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\MultisitePost;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\MultisitePost\Sources\MultisitePostPosts;

class GeneratorGroupMultisitePost extends AbstractGeneratorGroup {

    protected $name = 'msposts';

    public function getLabel() {
        return 'WordPress MultiSite';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('Multisite Posts'));
    }

    protected function loadSources() {

        foreach (get_sites(array('number' => null)) AS $site) {
            if ($site->blog_id == get_current_blog_id()) {
                continue;
            }

            $current_blog_details = get_blog_details(array('blog_id' => $site->blog_id));

            new MultisitePostPosts($this, 'posts' . $site->blog_id, $site->blog_id, 'Multisite - ' . n2_('Posts') . ' - ' . $current_blog_details->blogname);
        }
    }

    public function isInstalled() {
        return is_multisite();
    }
}