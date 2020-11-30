<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Html;


use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;
use tidy;

class ItemHtmlFrontend extends AbstractItemFrontend {

    private $scripts = array();

    public function render() {
        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml() {
        $owner = $this->layer->getOwner();

        $css = '';
        if ($cssCode = $this->data->get('css', '')) {
            $css = Html::style($cssCode);
        }

        return Html::tag("div", array(
            'class' => 'n2-notow'
        ), $this->closeTags('<div style="text-align:' . $this->data->get("textalign") . ';">' . $owner->fill($this->data->get("html")) . '</div>') . $css);
    }

    private function closeTags($html) {

        $html = Html::tag('div', array(
            'class' => 'n2-ss-item-content n2-ow'
        ), $html);

        if (class_exists('tidy', false)) {
            $tidy_config = array(
                'show-body-only'      => true,
                'wrap'                => 0,
                'new-blocklevel-tags' => 'menu,mytag,article,header,footer,section,nav,svg,path,g,a',
                'new-inline-tags'     => 'video,audio,canvas,ruby,rt,rp',
                'doctype'             => '<!DOCTYPE HTML>',
                'preserve-entities'   => true,
                'drop-empty-elements' => false,
                'drop-empty-paras'    => false
            );
            $tidy        = new tidy();

            $html = preg_replace_callback('/<script.*?>.*?<\/script>/ism', array(
                $this,
                'matchScript'
            ), $html);

            return $tidy->repairString($html, $tidy_config, 'UTF8') . implode('', $this->scripts);
        }

        return $html;
    }

    public function matchScript($matches) {
        $this->scripts[] = $matches[0];

        return '';
    }
}