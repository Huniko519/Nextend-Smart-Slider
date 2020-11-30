<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Json\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Misc\HttpClient;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class JsonUrl extends AbstractGenerator {

    protected $layout = 'text';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('JSON from url'));
    }

    public function renderFields($container) {
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $source = $filterGroup->createRow('source');

        new Text($source, 'sourcefile', 'JSON or XML url', '', array(
            'style' => 'width:600px;'
        ));

        $filter = $filterGroup->createRow('filter');

        new Select($filter, 'data_type', 'Data type', 0, array(
            'options' => array(
                0 => 'JSON',
                1 => 'XML'
            )
        ));

        new Select($filter, 'json_level', 'Level separation', 2, array(
            'tipLabel'       => n2_('Level separation'),
            'tipDescription' => n2_('JSON codes can be customized to have many different levels. From a code it is impossible to know from which level do you want to use the given datas on the different slides, so you have to select that level from this list.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1911-json-generator#filter',
            'options'        => array(
                1 => 'first level',
                2 => 'second level',
                3 => 'third level'
            )
        ));

        new Select($filter, 'remove_levels', 'Remove levels from result', 0, array(
            'options' => array(
                0 => 0,
                1 => 1,
                2 => 2,
                3 => 3
            )
        ));
    }

    protected function flatten_array($array, $parent = '', $basekey = '') {
        if (!is_array($array)) {
            return false;
        }
        $result = array();
        if (!empty($basekey)) {
            $result['base_name'] = $basekey;
        }
        foreach ($array as $key => $value) {
            $original_key = $key;
            if (!empty($parent)) {
                $key = $parent . '_' . $key;
            }
            $result[$key . '_name'] = $original_key;
            if (is_array($value)) {
                $result = array_merge($result, $this->flatten_array($value, $key));
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    protected function removeLevel($array) {
        $result = array();
        foreach ($array AS $key => $value) {
            if (is_array($value)) {
                $result = array_merge($result, $value);
            }
        }

        return $result;
    }

    protected function _getData($count, $startIndex) {
        $source  = $this->data->get('sourcefile', '');
        $data    = array();
        $content = HttpClient::get($source, array(
            'referer' => '',
            'error'   => false
        ));

        if (!$content) {
            Notification::error('The file on the given url is either empty or it cannot be accessed.');

            return null;
        }

        if (($this->data->get('data_type', 0) == 1) || (strtolower(substr($source, -4)) == '.xml')) {
            $xmlData = true;
            $xml     = @simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA);
            $content = json_encode((array)$xml);
        } else {
            $xmlData = false;
        }
        $json = json_decode($content, true);

        if (!is_array($json) || $json == array('0' => false)) {
            if ($xmlData) {
                Notification::error(sprintf(n2_('The given text is not valid XML! %1$sValidate your code%2$s to make sure it is correct.'),'<a href="https://www.xmlvalidation.com/" target="_blank">','</a>'));
            } else {
                Notification::error(sprintf(n2_('The given text is not valid JSON! %1$sValidate your code%2$s to make sure it is correct.'),'<a href="https://jsonlint.com/" target="_blank">','</a>'));
            }

            return null;
        }

        $remove_levels = intval($this->data->get('remove_levels', 0));
        if ($remove_levels != 0) {
            for ($i = 0; $i < $remove_levels; $i++) {
                $json = $this->removeLevel($json);
            }
        }

        switch ($this->data->get('json_level', 2)) {
            case 1:
                $data[] = $this->flatten_array($json);
                break;
            case 2:
                foreach ($json AS $key => $json_row) {
                    if (is_array($json_row)) {
                        $data[] = $this->flatten_array($json_row, '', $key);
                    }
                }
                break;
            case 3:
                $array_values = array_values($json);
                if (is_array($array_values)) {
                    $array_shift = array_shift($array_values);
                    if (is_array($array_shift) && !empty($array_shift)) {
                        foreach ($array_shift AS $key => $json_row) {
                            if (is_array($json_row)) {
                                $data[] = $this->flatten_array($json_row, '', $key);
                            }
                        }
                    }
                }
                break;

        }

        if (empty($data)) {
            Notification::error(n2_('Try to change the "Level separation" or "Remove levels from result" setting.'));
        } else {
            $data = array_slice($data, $startIndex, $count);
        }


        return $data;
    }
}