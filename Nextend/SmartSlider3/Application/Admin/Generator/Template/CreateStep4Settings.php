<?php

namespace Nextend\SmartSlider3\Application\Admin\Generator;

use Nextend\Framework\Asset\Js\Js;

/**
 * @var ViewGeneratorCreateStep4Settings $this
 */

$generatorGroup  = $this->getGeneratorGroup();
$generatorSource = $this->getGeneratorSource();

JS::addInline('new N2Classes.GeneratorAdd();');
?>

<form id="n2-ss-form-generator-add" action="<?php echo $this->getAjaxUrlGeneratorCreateSettings($this->getGeneratorGroup()
                                                                                                     ->getName(), $this->getGeneratorSource()
                                                                                                                       ->getName(), $this->getSliderID(), $this->getGroupID()); ?>" method="post">
    <?php

    $this->displayForm();
    ?>
    <input name="generator[group]" value="<?php echo $generatorGroup->getName(); ?>" type="hidden"/>
    <input name="generator[type]" value="<?php echo $generatorSource->getName(); ?>" type="hidden"/>
    <input name="slider-id" value="<?php echo $this->getSliderID(); ?>" type="hidden"/>
</form>