<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Grouping;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Form;
use Nextend\Framework\View\AbstractView;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonApply;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonCancel;
use Nextend\SmartSlider3\Application\Admin\Layout\LayoutIframe;
use Nextend\SmartSlider3Pro\Form\Element\ParticleSkin;

class ViewSliderParticle extends AbstractView {

    /** @var integer */
    protected $sliderID;

    public function display() {
        $this->layout = new LayoutIframe($this);

        $this->layout->setLabel(n2_('Particle effect'));

        $buttonCancel = new BlockButtonCancel($this);
        $buttonCancel->addAttribute('id', 'n2-ss-form-cancel');
        $buttonCancel->setBig();
        $this->layout->addAction($buttonCancel);

        $buttonSet = new BlockButtonApply($this);
        $buttonSet->addAttribute('id', 'n2-ss-form-save');
        $buttonSet->setBig();
        $this->layout->addAction($buttonSet);

        $this->layout->addContent($this->render('Particle'));

        $this->layout->render();
    }

    /**
     * @return integer
     */
    public function getSliderID() {
        return $this->sliderID;
    }

    /**
     * @param integer $sliderID
     */
    public function setSliderID($sliderID) {
        $this->sliderID = $sliderID;
    }

    public function renderForm() {

        $form = new Form($this, 'slider');

        $table = new ContainerTable($form->getContainer(), 'particle', n2_('Particle effect'));

        $settings = $table->createRow('row1');


        new ParticleSkin($settings, 'preset', n2_('Effect'), 0, array(
            'relatedValueFields' => array(
                array(
                    'values' => array(
                        'link',
                        'polygons',
                        'bloom',
                        'web',
                        'blackwidow',
                        'zodiac',
                        'fading-dots',
                        'pirouette',
                        'sparkling',
                        'custom'
                    ),
                    'field'  => array(
                        'slidermobile'
                    )
                ),
                array(
                    'values' => array(
                        'link',
                        'polygons',
                        'bloom',
                        'web',
                        'blackwidow',
                        'zodiac',
                        'fading-dots',
                        'pirouette',
                        'sparkling'
                    ),
                    'field'  => array(
                        'slidercustomization'
                    )
                ),
                array(
                    'values' => array(
                        'custom'
                    ),
                    'field'  => array(
                        'slidercustom',
                        'table-row-row2'
                    )
                )
            )
        ));

        $customization = new Grouping($settings, 'customization');

        new Color($customization, 'color', n2_('Color'), 'FFFFFF80', array(
            'alpha' => true
        ));

        new Color($customization, 'line-color', n2_('Line color'), 'FFFFFF66', array(
            'alpha' => true
        ));

        new NumberSlider($customization, 'speed', n2_('Speed'), 2, array(
            'style' => 'width:35px;',
            'min'   => 1,
            'max'   => 60
        ));

        new NumberSlider($customization, 'number', n2_('Number of particles'), 28, array(
            'style' => 'width:35px;',
            'min'   => 10,
            'max'   => 200
        ));

        new Select($customization, 'hover', n2_('Hover'), 'off', array(
            'options' => array(
                '0'       => n2_('Off'),
                'grab'    => n2_('Grab'),
                'bubble'  => n2_('Bubble'),
                'repulse' => n2_('Repulse')
            )
        ));

        new Select($customization, 'click', n2_('Click'), 'off', array(
            'options' => array(
                '0'       => n2_('Off'),
                'repulse' => n2_('Repulse'),
                'push'    => n2_('Push'),
                'remove'  => n2_('Remove'),
                'bubble'  => n2_('Bubble')
            )
        ));

        new Textarea($settings, 'custom', n2_('Custom'), '', array(
            'width'     => 480,
            'minHeight' => 200
        ));

        new OnOff($settings, 'mobile', n2_('Hide on mobile'), 0, array(
            'invert' => true
        ));

        $notice = $table->createRow('row2');

        new Notice($notice, 'instructions', 'Instructions', 'You can generate at <a target="_blank" href="http://vincentgarreau.com/particles.js/">http://vincentgarreau.com/particles.js/</a> Then <i>Download current config (json)</i> and paste content into the field.');

        echo $form->render();
    }
}