<?php

namespace Nextend\Framework\Asset\Css;

use Nextend\Framework\Asset\AbstractAsset;
use Nextend\Framework\Asset\Fonts\Google\Google;
use Nextend\Framework\Plugin;
use Nextend\Framework\Settings;
use Nextend\Framework\Url\Url;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\SmartSlider3Info;

class Asset extends AbstractAsset {

    public function __construct() {
        $this->cache = new Cache();
    }

    public function getOutput() {

        Google::build();

        Less\Less::build();

        $output = "";

        $this->urls = array_unique($this->urls);

        foreach ($this->urls as $url) {
            $output .= Html::style($this->filterSrc($url), true, array(
                    'media' => 'all'
                )) . "\n";
        }

        $needProtocol = !Settings::get('protocol-relative', '1');

        foreach ($this->getFiles() as $file) {
            if (substr($file, 0, 2) == '//') {
                $output .= Html::style($this->filterSrc($file), true, array(
                        'media' => 'all'
                    )) . "\n";
            } else {
                $output .= Html::style($this->filterSrc(Url::pathToUri($file, $needProtocol) . '?ver=' . SmartSlider3Info::$revisionShort), true, array(
                        'media' => 'all'
                    )) . "\n";
            }
        }

        $inline = implode("\n", $this->inline);
        if (!empty($inline)) {
            $output .= Html::style($inline);
        }

        return $output;
    }

    private function filterSrc($src) {
        return Plugin::applyFilters('n2_style_loader_src', $src);
    }

    public function get() {
        Google::build();
        Less\Less::build();

        return array(
            'url'    => $this->urls,
            'files'  => $this->getFiles(),
            'inline' => implode("\n", $this->inline)
        );
    }

    public function getAjaxOutput() {

        $output = implode("\n", $this->inline);

        return $output;
    }
}