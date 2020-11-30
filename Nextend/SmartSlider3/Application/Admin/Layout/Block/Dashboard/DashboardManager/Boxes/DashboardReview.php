<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes;

use Nextend\Framework\Platform\Platform;
use Nextend\SmartSlider3\SmartSlider3Info;

/**
 * @var BlockDashboardReview $this
 */
?>

<div class="n2_dashboard_manager_review" data-star="0">

    <div class="n2_dashboard_manager_review__logo">
        <i class="ssi_48 ssi_48--review0"></i>
        <i class="ssi_48 ssi_48--review1"></i>
        <i class="ssi_48 ssi_48--review2"></i>
        <i class="ssi_48 ssi_48--review3"></i>
        <i class="ssi_48 ssi_48--review4"></i>
        <i class="ssi_48 ssi_48--review5"></i>
    </div>

    <div class="n2_dashboard_manager_review__heading">
        <?php n2_e('Let us know how we\'re doing'); ?>
    </div>

    <div class="n2_dashboard_manager_review__paragraph">
        <?php n2_e('If you are happy with Smart Slider 3 and can take a minute please leave a review. This will help to spread its popularity and to make this plugin a better one.'); ?>
    </div>

    <div class="n2_dashboard_manager_review__star_selector">

        <div class="n2_dashboard_manager_review__star" data-star="1" data-href="https://smartslider3.com/suggestion/?utm_campaign=<?php echo SmartSlider3Info::$campaign; ?>&utm_source=dashboard-review-1&utm_medium=smartslider-<?php echo Platform::getName() . '-' . SmartSlider3Info::$plan; ?>">
            <i class="ssi_24 ssi_24--star"></i>
        </div>
        <div class="n2_dashboard_manager_review__star" data-star="2" data-href="https://smartslider3.com/suggestion/?utm_campaign=<?php echo SmartSlider3Info::$campaign; ?>&utm_source=dashboard-review-2&utm_medium=smartslider-<?php echo Platform::getName() . '-' . SmartSlider3Info::$plan; ?>">
            <i class="ssi_24 ssi_24--star"></i>
        </div>
        <div class="n2_dashboard_manager_review__star" data-star="3" data-href="https://smartslider3.com/satisfied-customer/?utm_campaign=<?php echo SmartSlider3Info::$campaign; ?>&utm_source=dashboard-review-3&utm_medium=smartslider-<?php echo Platform::getName() . '-' . SmartSlider3Info::$plan; ?>">
            <i class="ssi_24 ssi_24--star"></i>
        </div>
        <div class="n2_dashboard_manager_review__star" data-star="4" data-href="https://smartslider3.com/satisfied-customer/?utm_campaign=<?php echo SmartSlider3Info::$campaign; ?>&utm_source=dashboard-review-4&utm_medium=smartslider-<?php echo Platform::getName() . '-' . SmartSlider3Info::$plan; ?>">
            <i class="ssi_24 ssi_24--star"></i>
        </div>

        <?php
        $reviewUrl = 'https://smartslider3.com/redirect/wordpress-review.html?utm_campaign=' . SmartSlider3Info::$campaign . '&utm_source=dashboard-review-5&utm_medium=smartslider-' . Platform::getName() . '-' . SmartSlider3Info::$plan;
        ?>
        <div class="n2_dashboard_manager_review__star" data-star="5" data-href="<?php echo $reviewUrl; ?>">
            <i class="ssi_24 ssi_24--star"></i></div>

    </div>
    <div class="n2_dashboard_manager_review__label" data-star="0"><?php n2_e('Rate your experience'); ?></div>
    <div class="n2_dashboard_manager_review__label" data-star="1"><?php n2_e('Hated it'); ?></div>
    <div class="n2_dashboard_manager_review__label" data-star="2"><?php n2_e('Disliked it'); ?></div>
    <div class="n2_dashboard_manager_review__label" data-star="3"><?php n2_e('It was ok'); ?></div>
    <div class="n2_dashboard_manager_review__label" data-star="4"><?php n2_e('Liked it'); ?></div>
    <div class="n2_dashboard_manager_review__label" data-star="5"><?php n2_e('Loved it'); ?></div>

    <div class="n2_dashboard_manager_review__close">
        <i class="ssi_16 ssi_16--remove"></i>
    </div>
</div>

<script>
    N2R('documentReady', function ($) {
        var $box = $('.n2_dashboard_manager_review'),
            close = function () {
                N2Classes.AjaxHelper
                    .ajax({
                        type: "POST",
                        url: N2Classes.AjaxHelper.makeAjaxUrl(N2Classes.AjaxHelper.getAdminUrl('ss3-admin'), {
                            nextendcontroller: 'settings',
                            nextendaction: 'rated'
                        }),
                        dataType: 'json'
                    });

                $box.remove();
            };

        $('.n2_dashboard_manager_review__star')
            .on({
                mouseenter: function (e) {
                    $box.attr('data-star', $(e.currentTarget).data('star'));
                },
                mouseleave: function () {
                    $box.attr('data-star', 0);
                },
                click: function (e) {
                    window.open($(e.currentTarget).data('href'), '_blank');
                    close();
                }
            });

        $box.find('.n2_dashboard_manager_review__close')
            .on('click', close);
    });
</script>