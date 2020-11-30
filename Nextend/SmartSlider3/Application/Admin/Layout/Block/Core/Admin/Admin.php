<?php

namespace Nextend\SmartSlider3\Application\Admin\Layout\Block\Core\Admin;

use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Plugin;
use Nextend\SmartSlider3\Settings;
use Nextend\SmartSlider3\SmartSlider3Info;

/**
 * @var $this BlockAdmin
 */
SmartSlider3Info::initLicense();

?>

    <div <?php $this->renderAttributes(); ?>>
        <div class="n2_admin__header">
            <?php echo $this->getHeader(); ?>
        </div>
        <div class="n2_admin__content">
            <?php echo $this->getSubNavigation(); ?>
            <?php $this->displayTopBar(); ?>

            <?php echo $this->displayContent(); ?>
        </div>
        <?php
        Plugin::doAction('afterApplicationContent');
        ?>
    </div>

    <?php

Notification::show();