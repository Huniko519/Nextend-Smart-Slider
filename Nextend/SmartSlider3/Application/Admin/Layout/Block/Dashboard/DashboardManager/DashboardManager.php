<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager;

use Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes\BlockDashboardNewsletter;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes\BlockDashboardReview;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes\BlockDashboardUpgradePro;

/**
 * @var BlockDashboardManager $this
 */
?>
<div class="n2_dashboard_manager">
    <div class="n2_dashboard_manager__content">
        <?php
        $review = new BlockDashboardReview($this);
        $review->display();
    

        $newsletter = new BlockDashboardNewsletter($this);
        $newsletter->display();

        ?>
    </div>
</div>
