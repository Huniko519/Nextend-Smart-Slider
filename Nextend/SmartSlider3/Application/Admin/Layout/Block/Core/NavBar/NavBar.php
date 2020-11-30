<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\NavBar;

/**
 * @var $this BlockNavBar
 */
?>
<div class="n2_nav_bar">

    <?php $this->displayBreadCrumbs(); ?>

    <div class="n2_nav_bar__logo">
        <a href="<?php echo $this->getSidebarLink(); ?>" tabindex="-1">
            <?php echo $this->getLogo(); ?>
        </a>
    </div>
    <div class="n2_nav_bar__menu">
        <?php
        foreach ($this->getMenuItems() AS $menuItem):
            ?>
            <div class="n2_nav_bar__menuitem<?php echo $menuItem->isActive() ? ' n2_nav_bar__menuitem--active' : ''; ?>"><?php $menuItem->display(); ?></div>
        <?php
        endforeach;
        ?>
    </div>
</div>
