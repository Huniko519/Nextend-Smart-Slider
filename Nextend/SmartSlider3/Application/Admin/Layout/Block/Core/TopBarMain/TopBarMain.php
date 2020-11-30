<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\TopBarMain;

/**
 * @var $this BlockTopBarMain
 */
?>
<script type="text/javascript">
    N2R('documentReady', function ($) {
        $('#<?php echo $this->getID(); ?>').css('top', N2Classes.Window.getTopOffset() + 'px');
    });
</script>
<div id="<?php echo $this->getID(); ?>" class="n2_admin__top_bar n2_top_bar_main">
    <div class="n2_top_bar_main__primary">
        <?php
        $this->displayPrimary();
        ?>
    </div>
    <div class="n2_top_bar_main__secondary">
        <?php
        $this->displaySecondary();
        ?>
    </div>
</div>