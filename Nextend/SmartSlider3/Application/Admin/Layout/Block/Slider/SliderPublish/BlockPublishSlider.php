<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderPublish;

use Nextend\Framework\View\AbstractBlock;

class BlockPublishSlider extends AbstractBlock {

    /** @var int */
    protected $sliderID;

    public function display() {

        $this->renderTemplatePart('Common');
        $this->renderTemplatePart('WordPress');
    }

    /**
     * @return int
     */
    public function getSliderID() {
        return $this->sliderID;
    }

    /**
     * @param int $sliderID
     */
    public function setSliderID($sliderID) {
        $this->sliderID = $sliderID;
    }


}