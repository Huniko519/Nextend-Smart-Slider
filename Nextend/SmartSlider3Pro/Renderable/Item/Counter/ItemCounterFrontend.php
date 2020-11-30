<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Counter;


use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemCounterFrontend extends AbstractItemFrontend {

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $value      = intval($owner->fill($this->data->get('value')));
        $min        = min(0, $value);
        $startvalue = max(intval($owner->fill($this->data->get('startvalue'))), $min);
        $total      = max($startvalue, $value);
        $duration   = max(0, intval($this->data->get('animationduration')));

        if ($total != $min) {
            $toPercent = (min($value, $total) - $min) / ($total - $min);
            if ($duration == 0) {
                // We do not have animation
                $fromPercent = $toPercent;
            } else {
                $fromPercent = (min($startvalue, $total) - $min) / ($total - $min);
            }
        } else {
            $duration    = 0;
            $fromPercent = $toPercent = 0;
        }

        $label     = $owner->fill($this->data->get('label'));
        $placement = '';
        if (!empty($label)) {

            $fontLabel = $owner->addFont($this->data->get('fontlabel'), 'simple');

            $labelHTML = Html::tag('div', array(
                'class' => $fontLabel
            ), $label);
            $placement = $this->data->get('labelplacement');
        }

        $html = '';

        if ($placement == 'before') {
            $html .= $labelHTML;
        }

        $font = $owner->addFont($this->data->get('font'), 'simple');

        $pre             = $this->data->get('pre');
        $post            = $this->data->get('post');
        $countingDivHTML = Html::tag('div', array(
            'class' => 'n2-ss-item-counter-counting-div n2-ow ' . $font
        ), $pre . round($min + $fromPercent * ($total - $min)) . $post);

        $html .= Html::tag('div', array(
            'id'    => $this->id,
            'class' => 'n2-ow'
        ), $countingDivHTML);


        if ($placement == 'after') {
            $html .= $labelHTML;
        }

        $jsData = array(
            'name'        => 'counter',
            'pre'         => $pre,
            'post'        => $post,
            'fromPercent' => $fromPercent,
            'toPercent'   => $toPercent,
            'duration'    => $duration,
            'delay'       => $this->data->get('animationdelay'),
            'min'         => $min,
            'total'       => $total,
            'counting'    => '.n2-ss-item-counter-counting-div',
            'displayMode' => false
        );

        if ($this->isEditor && $owner->underEdit) {
            $owner->addScript('new N2Classes.CounterItemAdmin(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
        } else {
            $owner->addScript('new N2Classes.FrontendItemCounter(this, "#' . $this->id . '", ' . json_encode($jsData) . ');');
        }

        return Html::tag('div', array(
            'class' => 'n2-ss-item-content n2-ow'
        ), $html);
    }
}