<?php


namespace Nextend\SmartSlider3\Application\Admin\Layout\Helper;


class Breadcrumb {

    protected $label = '';
    protected $icon = '';
    protected $url = '#';

    protected $isActive = false;

    protected $classes = array('n2_breadcrumbs__breadcrumb_button');

    public function __construct($label, $icon, $url = '#') {

        $this->label = $label;
        $this->icon  = $icon;
        $this->url   = $url;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return bool
     */
    public function isActive() {
        return $this->isActive;
    }

    public function display() {
        $html = '';
        if (!empty($this->icon)) {
            $html .= '<i class="' . $this->icon . '"></i>';
        }

        $html .= '<span>' . $this->label . '</span>';

        if ($this->url == '#') {
            echo '<div class="' . $this->getClass() . '">' . $html . '</div>';
        } else {
            echo '<a class="' . $this->getClass() . '" href="' . $this->url . '">' . $html . '</a>';
        }
    }

    protected function getClass() {

        return implode(' ', $this->classes);
    }

    public function addClass($className) {
        $this->classes[] = $className;
    }
}