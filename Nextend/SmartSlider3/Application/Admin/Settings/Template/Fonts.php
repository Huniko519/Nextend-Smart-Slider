<?php

namespace Nextend\SmartSlider3\Application\Admin\Settings;

use Nextend\Framework\Asset\Js\Js;

/**
 * @var $this ViewSettingsFonts
 */

JS::addInline('new N2Classes.SettingsFonts();');

?>

<form id="n2-ss-form-settings-fonts" method="post" action="<?php echo $this->getAjaxUrlSettingsFonts(); ?>">
    <?php
    $this->renderForm();
    ?>
</form>
