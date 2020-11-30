<?php

namespace Nextend\SmartSlider3Pro\Form\Element;


use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Form\Element\Select\Skin;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;

class ParticleSkin extends Skin {

    protected $fixed = true;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        $labels = array(
            'link'        => n2_('Link'),
            'polygons'    => n2_('Polygons'),
            'bloom'       => n2_('Bloom'),
            'web'         => n2_('Web'),
            'blackwidow'  => n2_('Black widow'),
            'zodiac'      => n2_('Zodiac'),
            'fading-dots' => n2_('Fading dots'),
            'pirouette'   => n2_('Pirouette'),
            'sparkling'   => n2_('Sparkling'),
        );

        $this->options = array(
            '0' => array(
                'label'    => n2_('Disabled'),
                'settings' => array()
            )
        );

        $folder    = ResourceTranslator::toPath('$ss3-pro-frontend$/js/particle/presets/');
        $files     = Filesystem::files($folder);
        $extension = 'json';
        for ($i = 0; $i < count($files); $i++) {
            $pathInfo = pathinfo($files[$i]);
            if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {

                $jsProp = json_decode(Filesystem::readFile($folder . $pathInfo['filename'] . '.json'), true);

                $this->options[$pathInfo['filename']] = array(
                    'label'    => $labels[$pathInfo['filename']],
                    'settings' => array(
                        'color'      => substr($jsProp['particles']["color"]["value"], 1) . str_pad(dechex($jsProp['particles']["opacity"]["value"] * 255), 2, "0", STR_PAD_LEFT),
                        'line-color' => substr($jsProp['particles']["line_linked"]["color"], 1) . str_pad(dechex($jsProp['particles']["line_linked"]["opacity"] * 255), 2, "0", STR_PAD_LEFT),
                        'hover'      => $jsProp['interactivity']["events"]["onhover"]['enable'] ? $jsProp['interactivity']["events"]["onhover"]['mode'] : 0,
                        'click'      => $jsProp['interactivity']["events"]["onclick"]['enable'] ? $jsProp['interactivity']["events"]["onclick"]['mode'] : 0,
                        'number'     => $jsProp['particles']["number"]["value"],
                        'speed'      => $jsProp['particles']["move"]["speed"]
                    )
                );

            }
        }

        $this->options['custom'] = array(
            'label'    => n2_('Custom'),
            'settings' => array()
        );
    }
}