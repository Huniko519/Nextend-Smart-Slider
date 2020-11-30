<?php
namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderManager;

use Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderBox\BlockSliderBox;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Slider\SliderManager\ActionBar\BlockActionBar;

/**
 * @var BlockSliderManager $this
 */

$groupID = $this->getGroupID();

$orderBy          = $this->getOrderBy();
$orderByDirection = $this->getOrderByDirection();

$sliders = $this->getSliders('published');

?>
<div class="n2_slider_manager" data-groupid="<?php echo $groupID; ?>" data-orderby="<?php echo $orderBy; ?>" data-orderbydirection="<?php echo $orderByDirection; ?>">
    <?php

    $actionBar = new BlockActionBar($this);
    $actionBar->setSliderManager($this);
    $actionBar->display();

    ?>
    <div class="n2_slider_manager__content">

        <div class="n2_slider_manager__box n2_slider_manager__new_slider">
            <i class="n2_slider_manager__new_slider_icon ssi_48 ssi_48--plus"></i>
            <span class="n2_slider_manager__new_slider_label">
                <?php echo n2_('New project'); ?>
            </span>
        </div>
        <?php

        foreach ($sliders as $sliderObj) {

            $blockSliderBox = new BlockSliderBox($this);
            $blockSliderBox->setGroupID($groupID);
            $blockSliderBox->setSlider($sliderObj);
            $blockSliderBox->display();
        }
        ?>
    </div>
</div>