<?php

namespace Nextend\SmartSlider3\Application\Admin\Settings;

use Nextend\Framework\Asset\Js\Js;

/**
 * @var $this ViewSettingsFramework
 */

JS::addInline('new N2Classes.SettingsFramework();');
?>
<form id="n2-ss-form-settings-framework" method="post" action="<?php echo $this->getAjaxUrlSettingsFramework(); ?>">
    <?php
    $this->renderForm();
    ?>
</form>
