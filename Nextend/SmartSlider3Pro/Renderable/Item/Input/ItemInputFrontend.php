<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Input;


use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemInputFrontend extends AbstractItemFrontend {

    public function render() {
        $owner = $this->layer->getOwner();

        $style = $owner->addStyle($this->data->get('style'), 'heading');

        $inputFont  = $owner->addFont($this->data->get('inputfont'), 'paragraph');
        $inputStyle = $owner->addStyle($this->data->get('inputstyle'), 'heading');

        $slideSubmitAction = $this->data->get('submit');
        if (!empty($slideSubmitAction)) {
            $owner->addScript('$("#' . $this->id . '").closest(".n2-ss-slide").on("' . $this->data->get('submit') . '", function(e){$("#' . $this->id . '").trigger("submit")})');
        }

        $parameters     = explode('&', $owner->fill($this->data->get('parameters')));
        $parametersHTML = '';
        foreach ($parameters AS $parameter) {
            $parameter = explode('=', $parameter);
            if (count($parameter) == 2) {
                $parametersHTML .= Html::tag('input', array(
                    'type'  => 'hidden',
                    'name'  => $parameter[0],
                    'value' => $parameter[1],
                    'class' => 'n2-ow'
                ), false);
            }
        }


        $button      = '';
        $buttonLabel = $owner->fill($this->data->get('buttonlabel'));
        if (!empty($buttonLabel)) {

            $buttonFont  = $owner->addFont($this->data->get('buttonfont'), 'hover');
            $buttonStyle = $owner->addStyle($this->data->get('buttonstyle'), 'heading');

            $button = Html::tag('input', array(
                'encode' => false,
                'style'  => 'white-space:nowrap;',
                'type'   => 'submit',
                'value'  => $buttonLabel,
                'class'  => 'n2-form-button ' . $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), false);
        }

        return Html::tag('form', array(
            'class'    => 'n2-ss-item-input-form ' . $style . ' n2-ss-item-content n2-ow ' . $owner->fill($this->data->get('class', '')),
            'id'       => $this->id,
            'action'   => $owner->fill($this->data->get('action')),
            'method'   => $this->data->get('method'),
            'target'   => $this->data->get('target'),
            'onsubmit' => $this->data->get('onsubmit')
        ), Html::tag('input', array(
                'encode'      => false,
                'name'        => $owner->fill($this->data->get('name', '')),
                'type'        => 'text',
                'placeholder' => strip_tags($owner->fill($this->data->get('placeholder', ''))),
                'class'       => 'n2-input n2-ow ' . $inputFont . $inputStyle,
                'style'       => 'display: block; width: 100%;white-space:nowrap;',
                'onkeyup'     => $this->data->get('onkeyup')
            ), false) . $parametersHTML . $button);
    }

    public function renderAdminTemplate() {
        $owner = $this->layer->getOwner();

        $style = $owner->addStyle($this->data->get('style'), 'heading');


        $inputFont  = $owner->addFont($this->data->get('inputfont'), 'paragraph');
        $inputStyle = $owner->addStyle($this->data->get('inputstyle'), 'heading');

        $button      = '';
        $buttonLabel = $owner->fill($this->data->get('buttonlabel'));
        if (!empty($buttonLabel)) {
            $buttonFont  = $owner->addFont($this->data->get('buttonfont'), 'hover');
            $buttonStyle = $owner->addStyle($this->data->get('buttonstyle'), 'heading');

            $button = Html::tag('div', array(
                'style' => 'white-space:nowrap;',
                'class' => 'n2-form-button ' . $buttonFont . ' ' . $buttonStyle . ' n2-ow'
            ), $buttonLabel);
        }


        return Html::tag('div', array(
            'class' => 'n2-ss-item-input-form ' . $style . ' ' . $this->data->get('class', '') . ' n2-ow'
        ), "<div class='n2-input n2-ow " . $inputFont . " " . $inputStyle . "' style='white-space:nowrap;'>" . strip_tags($owner->fill($this->data->get('placeholder', ''))) . "</div>" . $button);

    }
}