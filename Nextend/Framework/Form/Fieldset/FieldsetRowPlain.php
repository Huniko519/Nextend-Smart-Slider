<?php


namespace Nextend\Framework\Form\Fieldset;


use Nextend\Framework\Form\AbstractField;

class FieldsetRowPlain extends FieldsetRow {

    public function renderContainer() {
        echo '<div class="n2_form__table_row_plain" data-field="table-row-plain-' . $this->name . '">';

        $element = $this->first;
        while ($element) {
            echo $this->decorateElement($element);

            $element = $element->getNext();
        }

        echo '</div>';
    }

    /**
     * @param AbstractField $element
     *
     * @return string
     */
    public function decorateElement($element) {

        ob_start();

        $element->displayElement();

        return ob_get_clean();
    }
}