<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Text\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class TextInput extends AbstractGenerator {

    protected $layout = 'text_generator';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('CSV from input'));
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new Textarea($filter, 'source', 'CSV', '', array(
            'width'  => 300,
            'height' => 200,
            'resize' => 'both'
        ));

        new Text($filter, 'delimiter', 'Column delimiter', ',', array(
            'style' => 'width:50px;'
        ));
    }

    protected function _getData($count, $startIndex) {
        $source    = $this->data->get('source', '');
        $delimiter = $this->data->get('delimiter', ',');

        if (empty($delimiter)) {
            $delimiter = ",";
        }
        $data = array();

        if (!empty($source)) {
            $i = 0;
            $k = 0;
            foreach (preg_split("/((\r?\n)|(\r\n?))/", $source) as $line) {
                if ($startIndex <= $i && ($count + $startIndex) > $i) {
                    $line  = rtrim($line, "\r\n");
                    $parts = explode($delimiter, $line);
                    $j     = 1;
                    foreach ($parts AS $part) {
                        $data[$k]['variable' . $j] = $part;
                        $j++;
                    }
                    $k++;
                }
                $i++;
            }
        }

        return $data;
    }
}