<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Elements;

use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Misc\OAuth\OAuth;
use Nextend\Framework\Notification\Notification;


class DribbbleProjects extends Select {

    /** @var  OAuth */
    protected $api;

    public function __construct($insertAt, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($insertAt, $name, $label, $default, $parameters);

        try {
            $this->api->CallAPI('https://api.dribbble.com/v2/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
            if (count($result)) {
                foreach ($result as $project) {
                    $this->options[$project->id] = $project->name;
                }
                if ($this->getValue() == '') {
                    $this->setValue($result[0]->id);
                }
            }
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }
    }

    protected function fetchElement() {
        return parent::fetchElement();
    }

    public function setApi($api) {
        $this->api = $api;
    }
}
