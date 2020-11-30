<?php


namespace Nextend\SmartSlider3\Application\Admin\Sliders\Pro;


use Nextend\Framework\View\AbstractView;
use Nextend\SmartSlider3\Application\Admin\Layout\LayoutDefault;
use Nextend\SmartSlider3\Application\Admin\TraitAdminUrl;

class ViewSlidersActivate extends AbstractView {

    use TraitAdminUrl;

    public function display() {

        $this->layout = new LayoutDefault($this);

        $this->layout->addContent($this->render('Activate'));

        $this->layout->render();
    }
}