<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\AdminIframe;


use Nextend\SmartSlider3\Settings;

/**
 * @var $this BlockAdminIframe
 */
?>
<div <?php $this->renderAttributes(); ?>>
    <div class="n2_iframe_application__nav_bar">
        <div class="n2_iframe_application__nav_bar_label">
            <?php echo $this->getLabel(); ?>
        </div>
        <div class="n2_iframe_application__nav_bar_actions">
            <?php
            foreach ($this->getActions() AS $action) {
                $action->display();
            }
            ?>
        </div>
    </div>
    <div class="n2_iframe_application__content">
        <?php $this->displayContent(); ?>
    </div>
</div>