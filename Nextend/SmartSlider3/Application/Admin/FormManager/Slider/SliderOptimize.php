<?php


namespace Nextend\SmartSlider3\Application\Admin\FormManager\Slider;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\FormTabbed;

class SliderOptimize extends AbstractSliderTab {

    /**
     * SliderOptimize constructor.
     *
     * @param FormTabbed $form
     */
    public function __construct($form) {
        parent::__construct($form);
        $this->optimizeLazyLoad();
    

        $this->optimizeSlide();
        $this->optimizeLayer();
    

        $this->other();
    }

    /**
     * @return string
     */
    protected function getName() {
        return 'optimize';
    }

    /**
     * @return string
     */
    protected function getLabel() {
        return n2_('Optimize');
    }

    protected function optimizeLazyLoad() {

        /**
         * Used for field removal: /optimize/optimize-lazyload
         */
        $table = new ContainerTable($this->tab, 'optimize-lazyload', n2_('Lazy load'));

        $row1 = $table->createRow('optimize-lazyload-1');

        new Select($row1, 'imageload', n2_('Loading mode'), '0', array(
            'options'            => array(
                '0' => n2_('Normal'),
                '2' => n2_('Delayed loading'),
                '1' => n2_('Lazy loading')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        '1'
                    ),
                    'field'  => array(
                        'sliderimageloadNeighborSlides'
                    )
                )
            ),
            'tipLabel'           => n2_('Loading mode'),
            'tipDescription'     => n2_('You can speed up your site\'s loading by delaying the slide background images.'),
            'tipLink'            => 'https://smartslider.helpscoutdocs.com/article/1801-slider-settings-optimize#lazy-load'
        ));
        new Number($row1, 'imageloadNeighborSlides', n2_('Load neighbor'), 0, array(
            'unit' => n2_x('slides', 'Unit'),
            'wide' => 3
        ));
    }

    protected function optimizeSlide() {

        $table = new ContainerTable($this->tab, 'optimize-slide', n2_('Optimize slide images'));

        new OnOff($table->getFieldsetLabel(), 'optimize', n2_('Optimize slide images'), 0, array(
            'relatedFieldsOn' => array(
                'table-rows-optimize-slide'
            )
        ));

        $row1 = $table->createRow('optimize-slide-1');

        $memoryLimitText = '';
        if (function_exists('ini_get')) {
            $memory_limit = ini_get('memory_limit');
            if (!empty($memory_limit)) {
                $memoryLimitText = ' ' . sprintf(n2_('Your current memory limit is %s.'), $memory_limit);
            }
        }
        new Number($row1, 'optimize-quality', n2_('Quality'), 70, array(
            'min'  => 0,
            'max'  => 100,
            'unit' => '%',
            'wide' => 3,
            'post' => 'break'
        ));
        new Warning($row1, 'optimize-notice', n2_('This feature requires high memory limit. If you do not have enough memory you will get a blank page on the frontend.') . $memoryLimitText);

        $row2 = $table->createRow('optimize-slide-2');

        new Number($row2, 'optimizeThumbnailWidth', n2_('Thumbnail width'), 100, array(
            'min'  => 0,
            'unit' => 'px',
            'wide' => 5
        ));
        new Number($row2, 'optimizeThumbnailHeight', n2_('Thumbnail height'), 60, array(
            'min'  => 0,
            'unit' => 'px',
            'wide' => 5
        ));

        $row3 = $table->createRow('optimize-slide-3');
        new OnOff($row3, 'optimize-background-image-custom', n2_('Resize background'), '0', array(
            'relatedFieldsOn' => array(
                'slideroptimize-background-image-width',
                'slideroptimize-background-image-height'
            )
        ));
        new Number($row3, 'optimize-background-image-width', n2_('Width'), 800, array(
            'min'  => 0,
            'unit' => 'px',
            'wide' => 5
        ));
        new Number($row3, 'optimize-background-image-height', n2_('Height'), 600, array(
            'min'  => 0,
            'unit' => 'px',
            'wide' => 5
        ));
    }

    protected function optimizeLayer() {

        $table = new ContainerTable($this->tab, 'optimize-layer', n2_('Optimize layer images'));

        $row1 = $table->createRow('optimize-layer-1');
        new OnOff($row1, 'layer-image-optimize', n2_('Resize'), '0', array(
            'relatedFieldsOn' => array(
                'sliderlayer-image-tablet',
                'sliderlayer-image-mobile'
            )
        ));
        new Number($row1, 'layer-image-tablet', n2_('Tablet scale'), 50, array(
            'min'  => 1,
            'max'  => 100,
            'unit' => '%',
            'wide' => 3
        ));
        new Number($row1, 'layer-image-mobile', n2_('Mobile scale'), 30, array(
            'min'  => 1,
            'max'  => 100,
            'unit' => '%',
            'wide' => 3
        ));

        $row2 = $table->createRow('optimize-layer-2');
        new OnOff($row2, 'layer-image-base64', 'Base64 embed', '0', array(
            'relatedFieldsOn' => array(
                'sliderlayer-image-base64-size'
            ),
            'tipLabel'        => n2_('Base64 embed'),
            'tipDescription'  => n2_('Embeds the layer images to the page source, reducing the requests.')
        ));
        new Number($row2, 'layer-image-base64-size', n2_('Max file size'), 50, array(
            'min'  => 0,
            'unit' => 'kb',
            'wide' => 5
        ));
    }

    protected function other() {
        $table = new ContainerTable($this->tab, 'optimize-other', n2_('Other'));

        $row1 = $table->createRow('optimize-other-1');
    
        if (defined('JETPACK__VERSION')) {
            new OnOff($row1, 'optimize-jetpack-photon', n2_('JetPack Photon image optimizer'), 0);
        }
        new OnOff($row1, 'slides-background-video-mobile', n2_('Background video on mobile'), 1);
    
    }
}