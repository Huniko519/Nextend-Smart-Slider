<?php


namespace Nextend\Framework\Form\Container\LayerWindow;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\ContainerGeneral;
use Nextend\Framework\Form\ContainerInterface;
use Nextend\Framework\View\Html;

class ContainerDesign extends ContainerGeneral {

    public function __construct(ContainerInterface $insertAt, $name) {
        parent::__construct($insertAt, $name);
    }

    public function renderContainer() {

        $id = 'n2_css_' . $this->name;

        echo Html::openTag('div', array(
            'id'    => $id,
            'class' => 'n2_ss_design_' . $this->name
        ));

        $element = $this->first;
        while ($element) {
            $element->renderContainer();
            $element = $element->getNext();
        }

        echo Html::closeTag('div');

        $options = array(
            'ajaxUrl' => $this->getForm()
                              ->createAjaxUrl('css/index')
        );

        Js::addInline('new N2Classes.BasicCSS(' . json_encode($id) . ', ' . json_encode($options) . ');');
    }
}