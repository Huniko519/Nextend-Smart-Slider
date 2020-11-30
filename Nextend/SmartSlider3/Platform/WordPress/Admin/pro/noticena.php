<?php

use Nextend\Framework\ResourceTranslator\ResourceTranslator;

$current = time();
if (mktime(0, 0, 0, 11, 26, 2020) <= $current && $current <= mktime(0, 0, 0, 12, 2, 2020)) {
    if (get_option('ss3_bf_2020') != '1') {

        add_action('admin_enqueue_scripts', function () {
            wp_enqueue_script('jquery');
        });

        add_action('admin_notices', function () {
            ?>
            <div class="notice notice-info is-dismissible" data-ss3dismissable="ss3_bf_2020" style="display:grid;grid-template-columns: 100px auto;padding-top: 25px; padding-bottom: 22px;">
                <img alt="Smart Slider 3" src="<?php echo ResourceTranslator::toUrl('$ss3-admin$/images/notice.png'); ?>" width="74px" height="74px" style="grid-row: 1 / 4; align-self: center;justify-self: center"/>
                <h3 style="margin:0;">Activate your Smart Slider 3 Pro now and save 40%</h3>
                <p style="margin:0 0 2px;">Don't miss out on our biggest sale of the year! Get your
                    <b>Smart Slider 3 Pro plan</b> with <b>40% OFF</b>! Limited time offer expires on December 1.</p>
                <p style="margin:0;">
                    <a class="button button-primary" href="https://smartslider3.com/pricing/?coupon=SAVE4020&utm_source=wpprona&utm_medium=wp&utm_campaign=bf20" target="_blank">
                        Buy Now</a>
                    <a class="button button-dismiss" href="#">Dismiss</a>
                </p>
            </div>
            <?php
        });

        add_action('admin_footer', function () {
            ?>
            <script type="text/javascript">
                (function ($) {
                    $(function () {
                        setTimeout(function () {
                            $('div[data-ss3dismissable] .notice-dismiss, div[data-ss3dismissable] .button-dismiss')
                                .on('click', function (e) {
                                    e.preventDefault();
                                    $.post(ajaxurl, {
                                        'action': 'ss3_dismiss_admin_notice',
                                        'nonce': <?php echo json_encode(wp_create_nonce('ss3-dismissible-notice')); ?>
                                    });
                                    $(e.target).closest('.is-dismissible').remove();
                                });
                        }, 1000);
                    });
                })(jQuery);
            </script>
            <?php
        });

        add_action('wp_ajax_ss3_dismiss_admin_notice', function () {
            check_ajax_referer('ss3-dismissible-notice', 'nonce');

            update_option('ss3_bf_2020', '1');
            wp_die();
        });
    }
}