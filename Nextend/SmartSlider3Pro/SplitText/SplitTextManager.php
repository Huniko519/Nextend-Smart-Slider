<?php

namespace Nextend\SmartSlider3Pro\SplitText;

use Nextend\Framework\Pattern\VisualManagerTrait;
use Nextend\SmartSlider3Pro\SplitText\Block\SplitTextManager\BlockSplitTextManager;

class SplitTextManager {

    use VisualManagerTrait;

    public function display() {

        $postBackgroundAnimationManagerBlock = new BlockSplitTextManager($this->MVCHelper);
        $postBackgroundAnimationManagerBlock->display();
    }
}