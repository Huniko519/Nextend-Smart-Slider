<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\TopBarGroup;

/**
 * @var $this BlockTopBarGroup
 */
?>

<div class="<?php echo implode(' ', $this->getClasses()); ?>">
    <div class="n2_top_bar_group__inner">
        <?php
        $this->displayBlocks();
        ?>
    </div>
</div>
