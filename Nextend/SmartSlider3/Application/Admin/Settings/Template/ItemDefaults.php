<?php

namespace Nextend\SmartSlider3\Application\Admin\Settings;


use Nextend\Framework\Asset\Js\Js;

/**
 * @var $this ViewSettingsItemDefaults
 */

JS::addInline('new N2Classes.SettingsItemDefaults();');
?>

<form id="n2-ss-form-settings-item-defaults" action="<?php echo $this->getAjaxUrlSettingsItemDefaults(); ?>" method="post">
    <?php
    $this->renderForm();
    ?>
</form>