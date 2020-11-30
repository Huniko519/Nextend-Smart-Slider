<?php
/**
 * @required N2SSPRO
 */

namespace Nextend\Framework\Form\Element;


use Nextend\Framework\Asset\Js\Js;

class Icon extends AbstractChooser {

    protected $hasClear = false;

    protected $class = ' n2_field_icon';

    protected function addScript() {

        \Nextend\Framework\Icon\Icon::serveAdmin();

        Js::addInline('
            new N2Classes.FormElementIcon2Manager("' . $this->fieldID . '");
        ');
    }

    protected function field() {
        return '<div class="n2_field_icon__preview"></div>';
    }
}