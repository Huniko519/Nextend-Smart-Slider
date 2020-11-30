<?php


namespace Nextend\SmartSlider3\Application\Admin\FormManager\Slider;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio\ImageListFromFolder;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\FieldImage;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\FormTabbed;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;

class SliderLoading extends AbstractSliderTab {

    /**
     * SliderLoading constructor.
     *
     * @param FormTabbed $form
     */
    public function __construct($form) {
        parent::__construct($form);

        $this->loading();
        $this->loadingAnimation();
    
    }

    /**
     * @return string
     */
    protected function getName() {
        return 'loading';
    }

    /**
     * @return string
     */
    protected function getLabel() {
        return n2_('Loading');
    }

    protected function loading() {

        $table = new ContainerTable($this->tab, 'loading', n2_('Loading'));

        $row0 = $table->createRow('loading-0');

        new Select($row0, 'loading-type', 'Loading type', '', array(
            'options'            => array(
                ''            => n2_('Instant'),
                'afterOnLoad' => n2_('After page loaded'),
                'afterDelay'  => n2_('After delay')
            ),
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'afterDelay'
                    ),
                    'field'  => array(
                        'sliderdelay'
                    )
                )
            ),
        ));

        new Number($row0, 'delay', n2_('Load delay'), 0, array(
            'wide' => 5,
            'unit' => 'ms'
        ));

        $row1 = $table->createRow('loading-1');

        new OnOff($row1, 'playWhenVisible', n2_('Play when visible'), 1, array(
            'relatedFieldsOn' => array(
                'sliderplayWhenVisibleAt'
            ),
            'tipLabel'        => n2_('Play when visible'),
            'tipDescription'  => n2_('Makes sure that the autoplay and layer animations only start when your slider is visible.')
        ));
        new Number($row1, 'playWhenVisibleAt', n2_('At'), 50, array(
            'unit' => '%',
            'wide' => 3
        ));

        $row2 = $table->createRow('loading-2');
        new OnOff($row2, 'fadeOnLoad', n2_('Fade on load'), 1, array(
            'relatedFieldsOn'  => array(
                'table-loading-animation'
            ),
            'relatedFieldsOff' => array(
                'table-row-loading-3'
            )
        ));
        new OnOff($row2, 'fadeOnScroll', n2_('Fade on scroll'), 0);
    

        new OnOff($row2, 'is-delayed', n2_('Delayed (for lightbox/tabs)'), 0);
        $row3 = $table->createRow('loading-3');
        new Warning($row3, 'loading-notice', n2_('If you turn off <b>Fade On Load</b>, the slider loading will be visible as the browser builds up the slider. In most cases this looks bad, so we don\'t suggest turning <b>Fade On Load</b> off.'));
    
    }

    protected function loadingAnimation() {
        $table = new ContainerTable($this->tab, 'loading-animation', n2_('Loading animation'));

        $row1 = $table->createRow('loading-animation-1');

        $fieldSpinner = new ImageListFromFolder($row1, 'spinner', n2_('Spinner'), 'simpleWhite', array(
            'tipLabel'     => n2_('Loading animation'),
            'folder'       => ResourceTranslator::toPath('$ss3-admin$/spinner/'),
            'filenameOnly' => true
        ));

        new FieldImage($fieldSpinner, 'custom-spinner', n2_('Custom'), '', array(
            'relatedFields' => array(
                'slidercustom-spinner-width',
                'slidercustom-spinner-height',
                'slidercustom-display'
            )
        ));

        new NumberAutoComplete($row1, 'custom-spinner-width', n2_('Width'), 100, array(
            'values' => array(
                300,
                200,
                100,
                50
            ),
            'unit'   => 'px',
            'wide'   => 4
        ));
        new NumberAutoComplete($row1, 'custom-spinner-height', n2_('Height'), 100, array(
            'values' => array(
                300,
                200,
                100,
                50
            ),
            'unit'   => 'px',
            'wide'   => 4
        ));
        new OnOff($row1, 'custom-display', n2_('Hide until complete load'), 1, array(
            'tipLabel'       => n2_('Hide until complete load'),
            'tipDescription' => n2_('When an image is used as the loading spinner, it takes time to load. This can be visible, as the image being built up by the browser and moves to the slider\'s center. You can hide the spinner during this image loading part.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1803-slider-settings-loading#loading-animation'
        ));


        $row3 = $table->createRow('loading-animation-3');
        new FieldImage($row3, 'placeholder-background-image', n2_('Background image'), '');
        new Color($row3, 'placeholder-color', n2_('Background color'), 'FFFFFF00', array(
            'alpha' => true
        ));
    
    }
}