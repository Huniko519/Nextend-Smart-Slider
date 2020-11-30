<?php


namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderManager;


use Nextend\Framework\Localization\Localization;
use Nextend\Framework\View\AbstractBlock;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Settings;

class BlockSliderManager extends AbstractBlock {

    protected $groupID = 0;

    protected $orderBy = 'ordering';

    protected $orderByDirection = 'ASC';

    public function display() {
        if ($this->groupID > 0) {
            $this->orderBy          = 'ordering';
            $this->orderByDirection = 'ASC';
        } else {
            $this->orderBy          = Settings::get('slidersOrder2', 'ordering');
            $this->orderByDirection = Settings::get('slidersOrder2Direction', 'ASC');
        }

        $this->renderTemplatePart('SliderManager');
    }

    /**
     * @return int
     */
    public function getGroupID() {
        return $this->groupID;
    }

    /**
     * @param int $groupID
     */
    public function setGroupID($groupID) {
        $this->groupID = $groupID;
    }

    public function getSliders($status = '*') {

        $slidersModel = new ModelSliders($this);

        return $slidersModel->getAll($this->groupID, $status, $this->orderBy, $this->orderByDirection);
    }

    /**
     * @return string
     */
    public function getOrderBy() {
        return $this->orderBy;
    }

    /**
     * @return string
     */
    public function getOrderByDirection() {
        return $this->orderByDirection;
    }

}