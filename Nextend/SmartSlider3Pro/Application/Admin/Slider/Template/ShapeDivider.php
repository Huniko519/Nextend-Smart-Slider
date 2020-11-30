<?php

namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Request\Request;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\SmartSlider3\Settings;
use Nextend\SmartSlider3\Slider\Slider;

/**
 * @var $this ViewSliderShapeDivider
 */

JS::addGlobalInline('document.documentElement.classList.add("n2_html--application-only");');

Js::addStaticGroup(ResourceTranslator::toPath('$ss3-pro-frontend$/dist/GsapMorphSVGPlugin.min.js'), 'GsapMorphSVGPlugin');

$postedSliderData                    = (array)Request::$POST->getVar('slider', false);
$postedSliderData['desktop']         = 1; // Shape divider does not work if slider is not visible.
$postedSliderData['playWhenVisible'] = 0;

$frontendSlider = new Slider($this, $this->getSliderID(), array(
    'disableResponsive' => true,
    'sliderData'        => $postedSliderData
), true);

$frontendSlider->initAll();
$frontendSlider->addScript('new N2Classes.DeviceChanger(this);');
$sliderHTML = $frontendSlider->render();


$externals = Settings::get('external-css-files');
if (!empty($externals)) {
    $externals = explode("\n", $externals);
    foreach ($externals AS $external) {
        echo "<link rel='stylesheet' href='" . $external . "' type='text/css' media='all' />";
    }
}

$folder = ResourceTranslator::toPath('$ss3-pro-frontend$/shapedivider/');

$files     = Filesystem::files($folder);
$extension = 'svg';
$types     = array();
for ($i = 0; $i < count($files); $i++) {
    $pathInfo = pathinfo($files[$i]);
    if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {
        $types['simple-' . $pathInfo['filename']] = file_get_contents($folder . $files[$i]);
    }
}

$folder .= 'bicolor/';
$files  = Filesystem::files($folder);
for ($i = 0; $i < count($files); $i++) {
    $pathInfo = pathinfo($files[$i]);
    if (isset($pathInfo['extension']) && $pathInfo['extension'] == $extension) {
        $types['bi-' . $pathInfo['filename']] = file_get_contents($folder . $files[$i]);
    }
}

Js::addFirstCode("    
    new N2Classes.ShapeDividerAdminManager(" . $this->getSliderID() . ", " . json_encode($types) . ");
");

$this->renderForm();
?>

<div class="n2_slider_preview_area">
    <div class="n2_slider_preview_area__inner">
        <?php
        echo $sliderHTML;
        ?>
    </div>
</div>