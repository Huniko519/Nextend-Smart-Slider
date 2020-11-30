<?php


namespace Nextend\SmartSlider3\Slider\Feature;


use Nextend\Framework\Model\Section;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Slider\Slide;
use Nextend\SmartSlider3Pro\PostBackgroundAnimation\PostBackgroundAnimationStorage;
class PostBackgroundAnimation {

    private $slider;

    private $hasSlideData = false;

    private $slideData = array();

    public function __construct($slider) {

        $this->slider = $slider;
    }

    /**
     * @param Slide $slide
     */
    public function makeSlide($slide) {
        $animations = $this->parseKenBurns($slide->parameters->get('kenburns-animation', '50|*|50|*|'));
        if ($animations) {
            $this->slideData[$slide->index] = array(
                'data'     => $animations,
                'speed'    => $slide->parameters->get('kenburns-animation-speed', 'default'),
                'strength' => $slide->parameters->get('kenburns-animation-strength', 'default')
            );
            $this->hasSlideData             = true;
        }
    }

    public function makeJavaScriptProperties(&$properties) {
        $properties['postBackgroundAnimations'] = array(
            'data'     => $this->parseKenBurns($this->slider->params->get('kenburns-animation', '50|*|50|*|')),
            'speed'    => $this->slider->params->get('kenburns-animation-speed', 'default'),
            'strength' => $this->slider->params->get('kenburns-animation-strength', 'default')
        );

        if ($this->hasSlideData) {
            $properties['postBackgroundAnimations']['slides'] = $this->slideData;
        } else if (!$properties['postBackgroundAnimations']['data']) {
            $properties['postBackgroundAnimations'] = 0;
        }
    }

    private function parseKenBurns($kenBurnsRaw) {

        $kenBurns   = Common::parse($kenBurnsRaw);
        $animations = array();
        if (is_array($kenBurns)) {
            if (count($kenBurns) >= 2) {
                $animations = array_unique(array_map('intval', (array)$kenBurns[2]));
            }
        }

        $jsProps = array();

        if (count($animations)) {
            PostBackgroundAnimationStorage::getInstance();

            foreach ($animations AS $animationId) {
                $animation = Section::getById($animationId, 'postbackgroundanimation');
                if (isset($animation)) {
                    $jsProps[] = $animation['value']['data'];
                }
            }
            if (count($jsProps)) {
                return array(
                    'transformOrigin' => $kenBurns[0] . '% ' . $kenBurns[1] . '%',
                    'animations'      => $jsProps
                );
            }
        }

        return 0;
    }
}
