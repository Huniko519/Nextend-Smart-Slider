<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderManager\ActionBar;

/**
 * @var BlockActionBar $this
 */
?>
<div class="n2_slider_manager__action_bar">
    <div class="n2_slider_manager__action_bar_left">
        <?php

        $this->displayOrderBy();

        $this->displayBulkActions();

        $this->displayCreateGroup();

        ?>
    </div>
    <div class="n2_slider_manager__action_bar_right">
        <?php
        $this->displayTrash();
        ?>
    </div>
</div>
