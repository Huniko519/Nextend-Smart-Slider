<?php


namespace Nextend\Framework\Form\Container;

use Nextend\Framework\Form\ContainerGeneral;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\Framework\Form\Fieldset\FieldsetTableLabel;

class ContainerTable extends ContainerGeneral {

    /**
     * @var FieldsetTableLabel
     */
    protected $fieldsetLabel;

    protected $fieldsetLabelPosition = 'start';

    public function __construct($insertAt, $name, $label = false, $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $parameters);

        $labelContainer      = new ContainerAlternative($this, $name . '-container-label');
        $this->fieldsetLabel = new FieldsetTableLabel($labelContainer, $name . '-label', false);
    }

    public function renderContainer() {
        echo '<div class="n2_form__table" data-field="table-' . $this->name . '">';
        echo '<div class="n2_form__table_label">';
        echo '<div class="n2_form__table_label_title">';
        echo $this->label;
        echo '</div>';
        if ($this->fieldsetLabel->hasFields()) {
            echo '<div class="n2_form__table_label_fields n2_form__table_label_fields--' . $this->fieldsetLabelPosition . '">';
            $this->fieldsetLabel->renderContainer();
            echo '</div>';
        }
        echo '</div>';

        if ($this->first) {
            echo '<div class="n2_form__table_rows" data-field="table-rows-' . $this->name . '">';
            parent::renderContainer();
            echo '</div>';
        }
        echo '</div>';
    }

    /**
     * @param $name
     *
     * @return FieldsetRow
     */
    public function createRow($name) {

        return new FieldsetRow($this, $name);
    }

    /**
     * @param        $name
     * @param string $label
     *
     * @return ContainerRowGroup
     */
    public function createRowGroup($name, $label) {

        return new ContainerRowGroup($this, $name, $label);
    }

    /**
     * @return FieldsetTableLabel
     */
    public function getFieldsetLabel() {
        return $this->fieldsetLabel;
    }

    public function setFieldsetPositionEnd() {
        $this->fieldsetLabelPosition = 'end';
    }

}