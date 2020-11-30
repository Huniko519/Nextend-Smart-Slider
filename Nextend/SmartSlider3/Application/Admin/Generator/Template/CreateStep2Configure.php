<?php

namespace Nextend\SmartSlider3\Application\Admin\Generator;

use Nextend\Framework\Asset\Js\Js;

/**
 * @var ViewGeneratorCreateStep2Configure $this
 */

JS::addInline('new N2Classes.GeneratorConfigure();');
?>
<form id="n2-ss-form-generator-configure" action="<?php echo $this->getAjaxUrlGeneratorCheckConfiguration($this->getGeneratorGroup()
                                                                                                               ->getName(), $this->getSliderID(), $this->getGroupID()); ?>" method="post">
    <?php
    echo $this->renderForm();
    ?>
</form>