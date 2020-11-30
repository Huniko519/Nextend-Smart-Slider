<?php


namespace Nextend\SmartSlider3Pro\SplitText;


use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Select\Easing;
use Nextend\Framework\Form\Element\Text\Color;
use Nextend\Framework\Form\Element\Text\Number;
use Nextend\Framework\Form\Element\Text\NumberAutoComplete;
use Nextend\Framework\Form\Element\Text\NumberSlider;
use Nextend\Framework\Form\Fieldset\FieldsetVisualSet;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Visual\ModelVisual;

class ModelSplitText extends ModelVisual {

    protected $type = 'splittextanimation';

    protected function init() {

        SplitTextStorage::getInstance();

        $this->storage = StorageSectionManager::getStorage('smartslider');
    }

    public function renderSetsForm() {

        $form = new Form($this, $this->type . 'set');
        $form->addClass('n2_fullscreen_editor__content_sidebar_top_bar');
        $form->setDark();

        $setsTab = new FieldsetVisualSet($form->getContainer(), 'splittextanimation-sets', n2_('Sets'));
        new Select($setsTab, 'sets', false, '');

        echo $form->render();
    }

    public function renderForm() {
        $form = new Form($this, 'n2-splittextanimation-editor');

        $table = new ContainerTable($form->getContainer(), 'splittextanimation-table', n2_('Text animation settings'));

        $table->setFieldsetPositionEnd();

        $firstRow = $table->createRow('firstrow');

        new Radio($firstRow, 'mode', n2_('Mode'), 'chars', array(
            'options' => array(
                'chars' => n2_('Chars'),
                'words' => n2_('Words')
            )
        ));

        new Select($firstRow, 'sort', n2_('Sort'), 'normal', array(
            'options' => array(
                'normal'        => n2_('Normal'),
                'reversed'      => n2_('Reversed'),
                'random'        => n2_('Random'),
                'side'          => n2_('Side'),
                'sideShifted'   => n2_('Side shifted'),
                'center'        => n2_('Center'),
                'centerShifted' => n2_('Center shifted')
            )
        ));

        new NumberAutoComplete($firstRow, 'duration', n2_('Duration'), 800, array(
            'style'  => 'width:40px;',
            'min'    => 0,
            'values' => array(
                500,
                800,
                1000,
                1500,
                2000
            ),
            'unit'   => 'ms'
        ));

        new NumberAutoComplete($firstRow, 'stagger', n2_('Stagger'), 50, array(
            'style'  => 'width:40px;',
            'values' => array(
                25,
                50,
                100,
                200,
                400
            ),
            'unit'   => 'ms'
        ));

        new Easing($firstRow, 'easing', n2_('Easing'), 'easeOutCubic');

        $transformOrigin = new Mixed($firstRow, 'transformorigin', n2_('Transform origin'), '50|*|50|*|0');

        new NumberAutoComplete($transformOrigin, 'transformorigin-1', false, '', array(
            'sublabel' => 'X',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'wide'     => 4
        ));

        new NumberAutoComplete($transformOrigin, 'transformorigin-2', false, '', array(
            'sublabel' => 'Y',
            'values'   => array(
                0,
                50,
                100
            ),
            'unit'     => '%',
            'wide'     => 4
        ));

        new Number($transformOrigin, 'transformorigin-3', false, '', array(
            'sublabel' => 'Z',
            'unit'     => 'px',
            'wide'     => 4
        ));

        $secondRow = $table->createRow('thirdrow');

        new NumberSlider($secondRow, 'opacity', n2_('Opacity'), 100, array(
            'style' => 'width:22px;',
            'min'   => 0,
            'max'   => 100,
            'unit'  => '%'
        ));

        new NumberAutoComplete($secondRow, 'scale', n2_('Scale'), 100, array(
            'style'  => 'width:40px;',
            'min'    => 0,
            'max'    => 9999,
            'values' => array(
                0,
                50,
                100,
                150,
                1000
            ),
            'unit'   => '%'
        ));

        $offset = new Mixed($secondRow, 'offset', n2_('Offset'), '0|*|0');

        new NumberAutoComplete($offset, 'offset-1', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'X',
            'values'   => array(
                -400,
                -200,
                -100,
                0,
                100,
                200,
                400
            ),
            'unit'     => 'px'
        ));

        new NumberAutoComplete($offset, 'offset-2', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Y',
            'values'   => array(
                -400,
                -200,
                -100,
                0,
                100,
                200,
                400
            ),
            'unit'     => 'px'
        ));

        $rotate = new Mixed($secondRow, 'rotate', n2_('Rotate'), '0|*|0|*|0');

        new NumberAutoComplete($rotate, 'rotate-1', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'X',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        new NumberAutoComplete($rotate, 'rotate-2', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Y',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        new NumberAutoComplete($rotate, 'rotate-3', false, '', array(
            'style'    => 'width:40px;',
            'sublabel' => 'Z',
            'values'   => array(
                0,
                90,
                180,
                -90,
                -180
            ),
            'unit'     => '°'
        ));

        $previewTable = new ContainerTable($form->getContainer(), 'splittextanimation-preview', n2_('Preview'));

        $previewTable->setFieldsetPositionEnd();

        new Color($previewTable->getFieldsetLabel(), 'preview-background', false, 'ced3d5');

        $form->render();
    }
}