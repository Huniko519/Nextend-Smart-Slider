<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Dashboard\DashboardManager\Boxes;

use Nextend\SmartSlider3\SmartSlider3Info;

/**
 * @var BlockDashboardUpgradePro $this
 */
?>
    <div class="n2_dashboard_manager_upgrade_pro">

        <div class="n2_dashboard_manager_upgrade_pro__logo">
            <i class="ssi_48 ssi_48--upgrade"></i>
        </div>

        <div class="n2_dashboard_manager_upgrade_pro__heading">
            <?php n2_e('Why upgrade to Smart Slider 3 Pro?'); ?>
        </div>

        <div class="n2_dashboard_manager_upgrade_pro__details">
            <div class="n2_dashboard_manager_upgrade_pro__details_col">
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/sample-sliders/', array('utm_source' => $this->getSource() . '-sample-sliders')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('180+ slider templates'); ?></div>
                </a>
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/layers/', array('utm_source' => $this->getSource() . '-layers')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('14 new layers'); ?></div>
                </a>
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/animations-and-effects/', array('utm_source' => $this->getSource() . '-animations')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('New animations & effects'); ?></div>
                </a>
            </div>
            <div class="n2_dashboard_manager_upgrade_pro__details_col">
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/slide-library/', array('utm_source' => $this->getSource() . '-slide-library')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('Full slide library access'); ?></div>
                </a>
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/features/', array('utm_source' => $this->getSource() . '-free-pro')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('Extra advanced options'); ?></div>
                </a>
                <a target="_blank" href="<?php echo SmartSlider3Info::decorateExternalUrl('https://smartslider3.com/help/', array('utm_source' => $this->getSource() . '-support')); ?>" class="n2_dashboard_manager_upgrade_pro__details_option">
                    <i class="ssi_16 ssi_16--filledcheck"></i>
                    <div class="n2_dashboard_manager_upgrade_pro__details_option_label"><?php n2_e('Lifetime update & support'); ?></div>
                </a>
            </div>
        </div>

        <a href="<?php echo SmartSlider3Info::getWhyProUrl(array('utm_source' => $this->getSource())); ?>" target="_blank" class="n2_dashboard_manager_upgrade_pro__button">
            <?php n2_e('Upgrade to Pro'); ?>
        </a>

        <?php
        if ($this->hasDismiss()):
            ?>
            <div class="n2_dashboard_manager_upgrade_pro__close">
                <i class="ssi_16 ssi_16--remove"></i>
            </div>
        <?php
        endif;
        ?>
    </div>

    <?php
if ($this->hasDismiss()):
    ?>
    <script>
        N2R('documentReady', function ($) {
            var $box = $('.n2_dashboard_manager_upgrade_pro'),
                close = function () {
                    N2Classes.AjaxHelper
                        .ajax({
                            type: "POST",
                            url: N2Classes.AjaxHelper.makeAjaxUrl(N2Classes.AjaxHelper.getAdminUrl('ss3-admin'), {
                                nextendcontroller: 'settings',
                                nextendaction: 'dismissupgradepro'
                            }),
                            dataType: 'json'
                        });

                    $box.remove();
                };

            $box.find('.n2_dashboard_manager_upgrade_pro__close')
                .on('click', close);
        });
    </script>
<?php
endif;
?>