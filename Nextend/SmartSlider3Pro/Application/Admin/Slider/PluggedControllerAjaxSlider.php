<?php


namespace Nextend\SmartSlider3Pro\Application\Admin\Slider;


use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\SmartSlider3\Application\Admin\Slider\ControllerAjaxSlider;
use Nextend\SmartSlider3\Application\Admin\Slider\ViewAjaxSliderBox;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Application\Model\ModelSlidersXRef;

class PluggedControllerAjaxSlider {

    /** @var ControllerAjaxSlider */
    protected $controller;

    public function __construct($controller) {
        $this->controller = $controller;

        $this->controller->addExternalAction('getGroupInfoBySliderID', array(
            $this,
            'getGroupInfoBySliderID'
        ));

        $this->controller->addExternalAction('changeGroup', array(
            $this,
            'changeGroup'
        ));

        $this->controller->addExternalAction('createGroup', array(
            $this,
            'actionCreateGroup'
        ));

        $this->controller->addExternalAction('addToGroup', array(
            $this,
            'actionAddToGroup'
        ));
    }

    public function getGroupInfoBySliderID() {
        $this->controller->validateToken();

        $this->controller->validatePermission('smartslider_edit');

        $sliderID = Request::$REQUEST->getInt('sliderID');
        $this->controller->validateVariable($sliderID, 'slider');

        $slidersModel = new ModelSliders($this->controller);

        $xref = new ModelSlidersXRef($this->controller);

        $this->controller->getResponse()
                         ->respond(array(
                             'groups'   => $slidersModel->getGroups('published'),
                             'linkedTo' => $xref->getGroupsIDs($sliderID)
                         ));
    }

    public function changeGroup() {


        $sliderID = Request::$REQUEST->getInt('sliderID');
        $this->controller->validateVariable($sliderID, 'slider');

        $toLink   = array_map('intval', Request::$POST->getVar('toLink', array()));
        $toDelete = array_map('intval', Request::$POST->getVar('toDelete', array()));

        $xref = new ModelSlidersXRef($this->controller);

        foreach ($toDelete as $groupID) {
            $xref->deleteXref($groupID, $sliderID);
        }

        foreach ($toLink as $groupID) {
            $xref->add($groupID, $sliderID);
        }

        $this->controller->getResponse()
                         ->respond();
    }

    public function actionCreateGroup() {
        $this->controller->validateToken();

        $this->controller->validatePermission('smartslider_edit');
        $slidersModel = new ModelSliders($this->controller);

        $title = Request::$REQUEST->getVar('title');
        $this->controller->validateVariable(!empty($title), 'group name');

        $slider = array(
            'type'  => 'group',
            'title' => $title
        );

        $sliderid = $slidersModel->create($slider);
        $slider   = $slidersModel->getWithThumbnail($sliderid);
        $this->controller->validateDatabase($slider);

        $view = new ViewAjaxSliderBox($this->controller);
        $view->setSlider($slider);

        $this->controller->getResponse()
                         ->respond($view->display());
    }

    public function actionAddToGroup() {
        $this->controller->validateToken();

        $this->controller->validatePermission('smartslider_edit');

        $actionType = Request::$REQUEST->getCmd('actionType');
        $this->controller->validateVariable($actionType, 'Action');

        $currentGroupID = Request::$REQUEST->getInt('currentGroupID', 0);

        $groupID = Request::$REQUEST->getInt('groupID');
        $this->controller->validateVariable($groupID, 'group');

        $sliders = Request::$REQUEST->getVar('sliders');
        if (!is_array($sliders)) {
            Notification::error(n2_('Missing sliders!'));
            $this->controller->getResponse()
                             ->error();
        }

        $slidersModel = new ModelSliders($this->controller);

        $xref = new ModelSlidersXRef($this->controller);
        foreach ($sliders as $sliderID) {
            switch ($actionType) {
                case 'copy':
                    $newSliderID = $slidersModel->duplicate($sliderID, false);
                    $xref->add($groupID, $newSliderID);
                    break;
                case 'link':
                    $xref->add($groupID, $sliderID);
                    break;
                default:
                    $xref->deleteXref($currentGroupID, $sliderID);
                    $xref->add($groupID, $sliderID);
                    break;
            }
        }
        $this->controller->getResponse()
                         ->respond();
    }

}