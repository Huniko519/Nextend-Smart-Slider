<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources\WooCommerceCategory;
use Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources\WooCommerceProductsByFilter;
use Nextend\SmartSlider3Pro\Generator\WordPress\WooCommerce\Sources\WooCommerceProductsByIds;

class GeneratorGroupWooCommerce extends AbstractGeneratorGroup {

    protected $name = 'woocommerce';

    protected $url = 'http://www.woothemes.com/woocommerce/';

    public function getLabel() {
        return 'WooCommerce';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'WooCommerce');
    }

    protected function loadSources() {

        new WooCommerceProductsByFilter($this, 'productsbyfilter', n2_('Products by filter'));
        new WooCommerceProductsByIds($this, 'productsbyids', n2_('Products by IDs and/or SKUs'));
        new WooCommerceCategory($this, 'categories', n2_('Categories'));
    }

    public function isInstalled() {
        return class_exists('WooCommerce', false);
    }
}
