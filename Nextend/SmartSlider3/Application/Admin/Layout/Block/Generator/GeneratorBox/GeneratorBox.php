<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Generator\GeneratorBox;

/**
 * @var $this BlockGeneratorBox
 */
?>
<div class="n2_slide_generator_box" style="background-image: url('<?php echo $this->getImageUrl(); ?>');">
    <div class="n2_slide_generator_box__title">
        <div class="n2_slide_generator_box__title_label">
            <div class="n2_slide_generator_box__title_label_inner">
                <?php
                $label = $this->getLabel();
                echo $label;
                ?>
            </div>
            <i class="ssi_16 ssi_16--info" data-tip-description="<?php echo $this->getDescription(); ?>" data-tip-label="<?php echo $label; ?>"></i>
        </div>
        <a href="<?php echo $this->getButtonLink(); ?>" target="<?php echo $this->getButtonLinkTarget(); ?>" class="n2_slide_generator_box__title_button">
            <?php echo $this->getButtonLabel(); ?>
        </a>
    </div>
</div>