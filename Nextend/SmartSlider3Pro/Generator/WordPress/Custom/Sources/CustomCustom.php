<?php

namespace Nextend\SmartSlider3Pro\Generator\WordPress\Custom\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Form\Fieldset\FieldsetRow;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class CustomCustom extends AbstractGenerator {

    protected $layout = 'image';

    private $generator = array();

    public function __construct($group, $generator) {
        $this->generator = $generator;
        parent::__construct($group, $generator['name'], $generator['label']);
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        foreach ($this->generator['options'] AS $k => $option) {
            $row = $filterGroup->createRow('filter' . $k);

            $fields = isset($option[0]) ? $option : array($option);

            foreach ($fields AS $field) {
                switch ($field['type']) {
                    case 'onoff':
                        $this->onOff($row, $field);
                        break;
                    case 'text':
                        $this->text($row, $field);
                        break;
                    case 'textarea':
                        $this->textArea($row, $field);
                        break;
                    case 'select':
                        $this->select($row, $field);
                        break;
                }
            }
        }
    }

    protected function _getData($count, $startIndex) {
        $result = call_user_func($this->generator['records'], array(
            'options'    => $this->data->_data,
            'slideCount' => $count,
            'startIndex' => $startIndex
        ));

        return $result;
    }

    private function setDefault($parameters, $default = '') {

        if (!isset($parameters['label'])) {
            $parameters['label'] = $parameters['name'];
        }

        if (!isset($parameters['default'])) {
            $parameters['default'] = $default;
        }

        return $parameters;
    }

    /**
     * @param FieldsetRow $parent
     * @param             $parameters
     */
    private function onOff($parent, $parameters) {

        $parameters = $this->setDefault($parameters, 0);

        new OnOff($parent, $parameters['name'], $parameters['label'], $parameters['default']);
    }

    /**
     * @param FieldsetRow $parent
     * @param             $parameters
     */
    private function text($parent, $parameters) {

        $parameters = $this->setDefault($parameters);

        new Text($parent, $parameters['name'], $parameters['label'], $parameters['default']);
    }

    /**
     * @param FieldsetRow $parent
     * @param             $parameters
     */
    private function textArea($parent, $parameters) {

        $parameters = $this->setDefault($parameters);

        $size = array();
        if (isset($parameters['width'])) {
            $size['width'] = $parameters['width'];
        }
        if (isset($parameters['height'])) {
            $size['height'] = $parameters['height'];
        }

        new Textarea($parent, $parameters['name'], $parameters['label'], $parameters['default'], $size);
    }

    /**
     * @param FieldsetRow $parent
     * @param             $parameters
     */
    private function select($parent, $parameters) {

        $parameters = array_merge(array(
            'options' => array(
                'none' => n2_('No options given')
            )
        ), $parameters);

        $parameters = $this->setDefault($parameters, array_values($parameters['options'])[0]);

        $options = array(
            'options' => $parameters['options']
        );

        if (!empty($parameters['multiple'])) {
            $options += array(
                'isMultiple' => true
            );

            if (!empty($parameters['size'])) {
                $options += array(
                    'size' => $parameters['size']
                );
            }
        }

        new Select($parent, $parameters['name'], $parameters['label'], $parameters['default'], $options);
    }
}