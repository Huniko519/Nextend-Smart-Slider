<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Slide\SlideBox;

use Nextend\Framework\Sanitize;

/**
 * @var BlockSlideBox $this
 */
?>

<div class="n2_slide_manager__box n2_slide_box <?php echo implode(' ', $this->getClasses()) ?>"
     data-slideid="<?php echo $this->getSlideId(); ?>"
    <?php echo $this->getGeneratorAttribute(); ?>>

    <div class="n2_slide_box__content" style="background-image: URL('<?php echo Sanitize::esc_attr($this->getThumbnailOptimized()); ?>');">

        <div class="n2_slide_box__slide_overlay">
            <a class="n2_slide_box__slide_overlay_link" href="<?php echo $this->getEditUrl(); ?>"></a>
            <a class="n2_slide_box__slide_overlay_edit_button" href="<?php echo $this->getEditUrl(); ?>">
                <?php
                n2_e('Edit');
                ?>
            </a>
            <div class="n2_slide_box__slide_select_tick">
                <i class="ssi_16 ssi_16--check"></i>
            </div>

            <div class="n2_slide_box__slide_actions">
                <a class="n2_slide_box__slide_action_more n2_button_icon n2_button_icon--small n2_button_icon--grey-dark" href="#"><i class="ssi_16 ssi_16--more"></i></a>
            </div>
        </div>

        <div class="n2_slide_box__details">
            <?php
            if ($this->isStaticSlide()):
                ?>
                <div class="n2_slide_box__details_static_slide"><?php echo n2_('Static overlay'); ?></div>
            <?php
            endif;
            ?>
            <?php
            if ($this->hasGenerator()):
                ?>
                <div class="n2_slide_box__details_generator"><?php echo $this->getGeneratorLabel(); ?></div>
            <?php
            endif;
            ?>
        </div>
    </div>

    <div class="n2_slide_box__footer">
        <div class="n2_slide_box__footer_title">
            <?php
            echo Sanitize::esc_html($this->getSlideTitle());
            ?>
        </div>

        <div class="n2_slide_box__footer_status">

            <?php
            $hiddenViews = $this->getHiddenDeviceText();
            ?>
            <a class="n2_slide_box__footer_status_hidden" href="<?php echo $this->getEditUrl(); ?>" data-n2tip="<?php echo $hiddenViews; ?>">
                <i class="ssi_16 ssi_16--hide"></i>
            </a>

            <div class="n2_slide_box__footer_status_first_slide" data-n2tip="<?php n2_e('First slide'); ?>">
                <i class="ssi_16 ssi_16--star"></i>
            </div>

            <a class="n2_slide_box__footer_status_published" href="<?php echo $this->getUnPublishUrl(); ?>" data-n2tip="<?php n2_e('Published'); ?>">
                <i class="ssi_16 ssi_16--filledcheck"></i>
            </a>

            <a class="n2_slide_box__footer_status_unpublished" href="<?php echo $this->getPublishUrl(); ?>" data-n2tip="<?php n2_e('Unpublished'); ?>">
                <i class="ssi_16 ssi_16--filledremove"></i>
            </a>
        </div>
    </div>
</div>