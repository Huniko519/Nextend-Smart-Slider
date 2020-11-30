<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes;

use Nextend\Framework\Platform\Platform;

/**
 * @var BlockDashboardNewsletter $this
 */
?>
<div class="n2_dashboard_manager_newsletter">

    <div class="n2_dashboard_manager_newsletter__logo">
        <i class="ssi_48 ssi_48--newsletter"></i>
    </div>

    <div class="n2_dashboard_manager_newsletter__heading">
        <?php n2_e('Don’t miss any update'); ?>
    </div>

    <div class="n2_dashboard_newsletter__paragraph">
        <?php n2_e('Join more than 120,000 subscribers and get access to the latest slider templates, tips, tutorials and other exclusive contents directly to your inbox.'); ?>
    </div>

    <form class="n2_dashboard_newsletter__form">
        <input type="hidden" name="<?php echo strtoupper(Platform::getName()); ?>" value="Yes"/>
        <input type="hidden" name="SOURCE" value="Smart Slider 3"/>
        <input type="email" name="EMAIL" value="<?php echo Platform::getUserEmail(); ?>" placeholder="Email" tabindex="-1"/>
    </form>

    <div class="n2_dashboard_manager_newsletter__button">
        <?php n2_e('Subscribe'); ?>
    </div>

    <div class="n2_dashboard_manager_newsletter__close">
        <i class="ssi_16 ssi_16--remove"></i>
    </div>
</div>

<script>
    N2R('documentReady', function ($) {
        var $box = $('.n2_dashboard_manager_newsletter'),
            close = function (e, action) {
                N2Classes.AjaxHelper
                    .ajax({
                        type: "POST",
                        url: N2Classes.AjaxHelper.makeAjaxUrl(N2Classes.AjaxHelper.getAdminUrl('ss3-admin'), {
                            nextendcontroller: 'settings',
                            nextendaction: action || 'dismissNewsletterDashboard'
                        }),
                        dataType: 'json'
                    });

                $box.remove();
            },
            $form = $('.n2_dashboard_newsletter__form')
                .on('submit', function (e) {
                    e.preventDefault();

                    N2Classes.AjaxHelper
                        .ajax({
                            type: "POST",
                            url: "https://secure.nextendweb.com/mailchimp/subscribe.php",
                            data: $form.serialize(),
                            dataType: 'json'
                        })
                        .done(function () {
                        });

                    close(e, 'subscribed');
                });

        $('.n2_dashboard_manager_newsletter__button')
            .on('click', function () {
                $form.submit();
            });

        $box.find('.n2_dashboard_manager_newsletter__close')
            .on('click', close);
    });
</script>