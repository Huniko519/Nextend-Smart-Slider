<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Elements;

use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Request\Request;

class VimeoToken extends Text {

    function fetchElement() {

        $authUrl = $this->getForm()
                        ->createAjaxUrl(array(
                            "generator/getAuthUrl",
                            array(
                                'group' => Request::$REQUEST->getVar('group'),
                                'type'  => Request::$REQUEST->getVar('type')
                            )
                        ));

        Js::addInline('new N2Classes.FormElementVimeoToken("' . $this->fieldID . '", "' . $authUrl . '");');

        return parent::fetchElement();
    }

    protected function post() {
        return '<a class="n2_field_text__choose_text" href="#">' . n2_('Request token') . '</a>';
    }
}


