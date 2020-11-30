<?php


namespace Nextend\SmartSlider3\Platform\WordPress\Integration\Gutenberg;


use Nextend\Framework\Pattern\GetAssetsPathTrait;
use Nextend\SmartSlider3\Platform\WordPress\Shortcode\Shortcode;

class Gutenberg {

    use GetAssetsPathTrait;

    public function __construct() {
        global $wp_version;
        if (version_compare($wp_version, '5.0', '>=')) {
            add_action('init', array(
                $this,
                'init'
            ));
        }
    }

    public function init() {
        wp_register_script('gutenberg-smartslider3', self::getAssetsUri() . '/dist/gutenberg-block.min.js', array(
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'wp-editor'
        ), null, true);

        register_block_type('nextend/smartslider3', array(
            'editor_script' => 'gutenberg-smartslider3',
        ));

        add_action('enqueue_block_editor_assets', array(
            $this,
            'enqueue_block_editor_assets'
        ));
    }

    public function enqueue_block_editor_assets() {

        wp_add_inline_script('gutenberg-smartslider3', 'window.gutenberg_smartslider3=' . json_encode(array(
                'template' => Shortcode::renderIframe('{{{slider}}}')
            )) . ';');

        Shortcode::forceIframe('gutenberg');
    }
}