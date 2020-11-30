<?php

namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;

use Nextend\Framework\Asset\Js\Js;
use Nextend\SmartSlider3\Settings;

/**
 * @var $this ViewSliderEditGroup
 */

$slider = $this->getSlider();

JS::addInline('new N2Classes.GroupEdit(' . json_encode(array(
        'previewInNewWindow' => !!Settings::get('preview-new-window', 0),
        'saveAjaxUrl'        => $this->getAjaxUrlSliderEdit($slider['id']),
        'previewUrl'         => $this->getUrlPreviewSlider($slider['id']),
        'ajaxUrl'            => $this->getAjaxUrlSliderEdit($slider['id']),
        'formData'           => $this->getFormData()
    )) . ');');
?>

<div class="n2-ss-sliders-outer-container">
    <?php
    $this->renderSliderManager();
    ?>
</div>
<form id="n2-ss-edit-group-form" action="#" method="post">
    <?php
    $this->renderForm();
    ?>
</form>