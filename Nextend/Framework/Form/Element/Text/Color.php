<?php


namespace Nextend\Framework\Form\Element\Text;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\Text;

class Color extends Text {

    protected $alpha = false;

    protected $class = 'n2_field_color ';

    protected function fetchElement() {

        if ($this->alpha) {
            $this->class .= 'n2_field_color--alpha ';
        }

        $html = parent::fetchElement();
        Js::addInline('new N2Classes.FormElementColor("' . $this->fieldID . '", ' . intval($this->alpha) . ');');

        return $html;
    }

    protected function pre() {
        return '<div class="n2-sp-replacer"><div class="n2-sp-preview" style="background-color: rgb(62, 62, 62);"></div></div>';
    }

    protected function post() {
        return '';
    }

    /**
     * @param boolean $alpha
     */
    public function setAlpha($alpha) {
        $this->alpha = $alpha;
    }
}