<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\Banner;

/**
 * @var $this BlockBanner
 */

$closeUrl = $this->getCloseUrl();
?>

<div id="<?php echo $this->getID(); ?>" class="n2_admin__banner">
    <div class="n2_admin__banner_inner">
        <img src="<?php echo $this->getImage(); ?>" alt=""/>
        <div class="n2_admin__banner_inner_title"><?php echo $this->getTitle(); ?></div>
        <div class="n2_admin__banner_inner_description"><?php echo $this->getDescription(); ?></div>
        <a class="n2_admin__banner_inner_button n2_button n2_button--big n2_button--green"
           href="<?php echo $this->getButtonHref(); ?>"
           onclick="<?php echo $this->getButtonOnclick(); ?>"
           target="_blank">
            <?php echo $this->getButtonTitle(); ?>
        </a>
    </div>
    <?php if (!empty($closeUrl)): ?>
        <div class="n2_admin__banner_close">
            <i class="ssi_16 ssi_16--remove"></i>
        </div>

        <script>
            N2R('documentReady', function ($) {
                var $banner = $('#<?php echo $this->getID(); ?>');

                $banner.find('.n2_admin__banner_close').on('click', function (e) {
                    e.preventDefault();

                    N2Classes.AjaxHelper.ajax({url: <?php echo json_encode($closeUrl); ?>});
                    $banner.remove();
                });
            });
        </script>
    <?php endif; ?>
</div>