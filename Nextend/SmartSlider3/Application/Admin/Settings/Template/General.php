<?php

namespace Nextend\SmartSlider3\Application\Admin\Settings;


use Nextend\Framework\Asset\Js\Js;

/**
 * @var $this ViewSettingsGeneral
 */

JS::addInline('new N2Classes.SettingsGeneral();');
?>

<form id="n2-ss-form-settings-general" action="<?php echo $this->getAjaxUrlSettingsDefault(); ?>" method="post">
    <?php
    $this->renderForm();
    ?>
</form>