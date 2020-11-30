<?php

namespace Nextend\SmartSlider3\Application\Admin\Sliders;


use Nextend\Framework\Asset\Js\Js;


/**
 * @var ViewSlidersImport $this
 */

JS::addInline('new N2Classes.SliderImport();');
?>

<form id="n2-ss-form-slider-import" action="<?php echo $this->getAjaxUrlImport($this->getGroupID()); ?>" method="post">
    <?php
    $this->renderForm();
    ?>
</form>