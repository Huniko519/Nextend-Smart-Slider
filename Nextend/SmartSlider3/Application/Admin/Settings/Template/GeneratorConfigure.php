<?php

namespace Nextend\SmartSlider3\Application\Admin\Settings;

use Nextend\Framework\Asset\Js\Js;

/**
 * @var ViewGeneratorConfigure $this
 */

JS::addInline('new N2Classes.GeneratorConfigure();');
?>
<form id="n2-ss-form-generator-configure" action="<?php echo $this->getAjaxUrlSettingsGenerator($this->getGeneratorGroup()
                                                                                                     ->getName()); ?>" method="post">
    <?php
    echo $this->renderForm();
    ?>
</form>

<div style="height: 200px"></div>