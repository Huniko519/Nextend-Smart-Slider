<?php


namespace Nextend\SmartSlider3Pro\PostBackgroundAnimation\Block\PostBackgroundAnimationManager;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Visual\AbstractBlockVisual;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonApply;
use Nextend\SmartSlider3\Application\Admin\Layout\Block\Forms\Button\BlockButtonCancel;
use Nextend\SmartSlider3Pro\PostBackgroundAnimation\ModelPostBackgroundAnimation;

class BlockPostBackgroundAnimationManager extends AbstractBlockVisual {

    /** @var ModelPostBackgroundAnimation */
    protected $model;

    /**
     * @return ModelPostBackgroundAnimation
     */
    public function getModel() {
        return $this->model;
    }

    public function display() {

        $this->model = new ModelPostBackgroundAnimation($this);

        $this->renderTemplatePart('Index');
    }

    public function displayTopBar() {

        $buttonCancel = new BlockButtonCancel($this);
        $buttonCancel->addClass('n2_fullscreen_editor__cancel');
        $buttonCancel->display();

        $buttonApply = new BlockButtonApply($this);
        $buttonApply->addClass('n2_fullscreen_editor__save');
        $buttonApply->display();
    }

    public function displayContent() {
        $model = $this->getModel();

        $sets = $model->getSets();

        Js::addFirstCode("
            new N2Classes.PostBgAnimationManager({
                setsIdentifier: '" . $model->getType() . "set',
                sets: " . json_encode($sets) . ",
                visuals: {},
                ajaxUrl: '" . $this->createAjaxUrl(array('postbackgroundanimation/index')) . "'
            });
        ");

        $model->renderForm();
    }
}