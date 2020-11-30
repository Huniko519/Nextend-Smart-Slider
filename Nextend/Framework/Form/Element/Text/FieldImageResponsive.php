<?php


namespace Nextend\Framework\Form\Element\Text;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Image\ImageManager;
use Nextend\Framework\View\Html;

class FieldImageResponsive extends FieldImage {

    protected function fetchElement() {
        ImageManager::enqueue($this->getForm());
    

        $html = parent::fetchElement();

        $html .= Html::tag('a', array(
            'id'         => $this->fieldID . '_manage',
            'class'      => 'n2_field_button n2_field_button--icon n2_field_text_image__button',
            'href'       => '#',
            'data-n2tip' => n2_('Select images for devices')
        ), '<i class="ssi_16 ssi_16--desktopportrait"></i>');

        Js::addInline('new N2Classes.FormElementImageManager("' . $this->fieldID . '", {});');
    

        return $html;
    }
}