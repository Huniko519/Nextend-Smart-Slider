<?php


namespace Nextend\SmartSlider3Pro\PostBackgroundAnimation;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Fieldset\FieldsetVisualSet;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Visual\ModelVisual;

class ModelPostBackgroundAnimation extends ModelVisual {

    protected $type = 'postbackgroundanimation';

    protected function init() {

        PostBackgroundAnimationStorage::getInstance();

        $this->storage = StorageSectionManager::getStorage('smartslider');
    }

    protected function getPath() {
        return dirname(__FILE__);
    }

    public function renderSetsForm() {

        $form = new Form($this, $this->type . 'set');
        $form->addClass('n2_fullscreen_editor__content_sidebar_top_bar');
        $form->setDark();

        $setsTab = new FieldsetVisualSet($form->getContainer(), 'postbackgroundanimation-sets', n2_('Animation type'));
        new Select($setsTab, 'sets', false);

        echo $form->render();
    }

    public function renderForm() {
        $form = new Form($this, 'n2-post-background');

        $table = new ContainerTable($form->getContainer(), 'post-background-preview', n2_('Preview'));

        $table->setFieldsetPositionEnd();

        new NumberAutoComplete($table->getFieldsetLabel(), 'transformorigin-x', false, '50', array(
            'style'    => 'width:22px;',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'sublabel' => 'X'
        ));

        new NumberAutoComplete($table->getFieldsetLabel(), 'transformorigin-y', false, '50', array(
            'style'    => 'width:22px;',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'sublabel' => 'Y'
        ));

        $form->render();
    }
}