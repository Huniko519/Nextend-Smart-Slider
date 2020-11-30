<?php


namespace Nextend\Framework\Form\Fieldset;

use Nextend\Framework\Form\AbstractFieldset;

class FieldsetHidden extends AbstractFieldset {

    public function __construct($insertAt) {

        parent::__construct($insertAt, '');
    }

    public function renderContainer() {

        if ($this->first) {
            echo '<div class="n2_form_element--hidden">';

            $element = $this->first;
            while ($element) {
                echo $this->decorateElement($element);

                $element = $element->getNext();
            }

            echo '</div>';
        }
    }

}