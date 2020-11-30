<?php

namespace Nextend\SmartSlider3Pro\SplitText\Block\SplitTextManager;

use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonSave;

/**
 * @var BlockSplitTextManager $this
 */
?>

<div class="n2_fullscreen_editor__save_as_new_container">
    <?php
    $saveAsNew = new BlockButtonSave($this);
    $saveAsNew->addClass('n2_fullscreen_editor__save_as_new');
    $saveAsNew->setLabel(n2_('Save as new animation'));
    $saveAsNew->display();
    ?>
</div>
