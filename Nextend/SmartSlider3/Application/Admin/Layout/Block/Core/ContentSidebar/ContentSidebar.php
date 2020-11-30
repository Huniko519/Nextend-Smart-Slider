<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\ContentSidebar;

/**
 * @var $this BlockContentSidebar
 */
?>
<div class="n2-admin-content-with-sidebar">
    <div class="n2-admin-content-with-sidebar__sidebar">
        <?php
        echo $this->getSidebar();
        ?>
    </div>
    <div class="n2-admin-content-with-sidebar__content">
        <?php
        echo $this->getContent();
        ?>
    </div>
</div>