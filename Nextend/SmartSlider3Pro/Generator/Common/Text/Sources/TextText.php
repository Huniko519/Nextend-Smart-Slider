<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Text\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Misc\HttpClient;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class TextText extends AbstractGenerator {

    protected $layout = 'text_generator';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('CSV from url'));
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $source = $filterGroup->createRow('source');

        new Text($source, 'sourcefile', 'CSV url', '', array(
            'style' => 'width:600px;'
        ));

        $delimiter = $filterGroup->createRow('delimiter-row');

        new Text($delimiter, 'delimiter', 'Column delimiter', ',', array(
            'style' => 'width:50px;'
        ));
    }

    protected function _getData($count, $startIndex) {
        $delimiter = $this->data->get('delimiter', ',');
        $source    = $this->data->get('sourcefile', '');

        $content = HttpClient::get($source, array(
            'error' => false
        ));

        if (!$content) {
            Notification::error('The file on the given url is either empty or it cannot be accessed.');

            return null;
        }

        $lines = preg_split('/$\R?^/m', $content);
        $data  = array();
        if (!empty($lines)) {
            $k = 0;
            for ($i = 0; $i < count($lines) && ($count + $startIndex) > $i; $i++) {
                if ($startIndex <= $i) {
                    $parts = explode($delimiter, $lines[$i]);
                    $j     = 1;
                    foreach ($parts AS $part) {
                        $data[$k]['variable' . $j] = $part;
                        $j++;
                    }
                    $k++;
                }
            }
        }

        return $data;
    }
}