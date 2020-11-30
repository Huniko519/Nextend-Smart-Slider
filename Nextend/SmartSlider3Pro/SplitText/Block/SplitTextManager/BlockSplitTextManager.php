<?php


namespace Nextend\SmartSlider3Pro\SplitText\Block\SplitTextManager;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Visual\AbstractBlockVisual;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonCancel;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonSave;
use Nextend\SmartSlider3Pro\SplitText\ModelSplitText;
use Nextend\SmartSlider3Pro\SplitText\SplitTextRenderer;

class BlockSplitTextManager extends AbstractBlockVisual {

    /** @var ModelSplitText */
    protected $model;

    /**
     * @return ModelSplitText
     */
    public function getModel() {
        return $this->model;
    }

    public function display() {

        $this->model = new ModelSplitText($this);

        $this->renderTemplatePart('Index');
    }

    public function displayTopBar() {

        $buttonCancel = new BlockButtonCancel($this);
        $buttonCancel->addClass('n2_fullscreen_editor__cancel');
        $buttonCancel->display();

        $buttonApply = new BlockButtonSave($this);
        $buttonApply->setLabel(n2_('Apply'));
        $buttonApply->addClass('n2_fullscreen_editor__save');
        $buttonApply->display();
    }

    public function displaySidebar() {

        $this->renderTemplatePart('Sidebar');
    }

    public function displayContent() {

        $model = $this->getModel();


        $sets = $model->getSets();

        SplitTextRenderer::$sets[] = $sets[0]['id'];

        $animations = array();
        foreach (array_unique(SplitTextRenderer::$sets) AS $setId) {
            $animations[$setId] = $model->getVisuals($setId);
        }

        Js::addFirstCode("
            new N2Classes.NextendSplitTextAnimationManager({
                fixedSet: 1000,
                sets: " . json_encode($sets) . ",
                visuals: " . json_encode($animations) . ",
                ajaxUrl: '" . $this->createAjaxUrl(array('splittextanimation/index')) . "'
            });
        ");

        $model->renderForm();
    }
}