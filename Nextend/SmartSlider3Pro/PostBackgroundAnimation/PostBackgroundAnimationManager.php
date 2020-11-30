<?php


namespace Nextend\SmartSlider3Pro\PostBackgroundAnimation;


use Nextend\Framework\Pattern\VisualManagerTrait;
use Nextend\SmartSlider3Pro\PostBackgroundAnimation\Block\PostBackgroundAnimationManager\BlockPostBackgroundAnimationManager;

class PostBackgroundAnimationManager {

    use VisualManagerTrait;

    public function display() {

        $postBackgroundAnimationManagerBlock = new BlockPostBackgroundAnimationManager($this->MVCHelper);
        $postBackgroundAnimationManagerBlock->display();
    }

}