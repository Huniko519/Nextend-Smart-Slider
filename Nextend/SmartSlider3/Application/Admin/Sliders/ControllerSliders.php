<?php


namespace Nextend\SmartSlider3\Application\Admin\Sliders;


use Nextend\Framework\Localization\Localization;
use Nextend\Framework\Misc\Zip\Creator;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\PageFlow;
use Nextend\Framework\Request\Request;
use Nextend\SmartSlider3\Application\Admin\AbstractControllerAdmin;
use Nextend\SmartSlider3\Application\Admin\Sliders\Pro\ViewSlidersActivate;
use Nextend\SmartSlider3\Application\Model\ModelLicense;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\BackupSlider\ExportSlider;
use Nextend\SmartSlider3\Settings;

class ControllerSliders extends AbstractControllerAdmin {

    protected function actionGettingStarted() {

        if (!StorageSectionManager::getStorage('smartslider')
                                  ->get('tutorial', 'GettingStarted')) {

            $view = new ViewSlidersGettingStarted($this);

            $view->display();
        } else {
            $modelLicense = ModelLicense::getInstance();
            if (!$modelLicense->hasKey()) {

                $view = new ViewSlidersActivate($this);
                $view->display();

            } else {
                $this->redirectToSliders();
            }
        }
    }

    protected function actionGettingStartedDontShow() {
        StorageSectionManager::getStorage('smartslider')
                             ->set('tutorial', 'GettingStarted', 1);

        $this->redirectToSliders();
    }

    protected function actionIndex() {

        $this->loadSliderManager();

        $view = new ViewSlidersIndex($this);

        $view->display();
    }

    protected function actionTrash() {

        $view = new ViewSlidersTrash($this);

        $view->display();
    }

    protected function actionOrderBy() {
        $ordering = Request::$REQUEST->getCmd('ordering', null);
        if ($ordering == 'DESC' || $ordering == 'ASC') {
            Settings::set('slidersOrder2', 'ordering');
            Settings::set('slidersOrder2Direction', 'ASC');
        }

        $time = Request::$REQUEST->getCmd('time', null);
        if ($time == 'DESC' || $time == 'ASC') {
            Settings::set('slidersOrder2', 'time');
            Settings::set('slidersOrder2Direction', $time);
        }
        $title = Request::$REQUEST->getCmd('title', null);
        if ($title == 'DESC' || $title == 'ASC') {
            Settings::set('slidersOrder2', 'title');
            Settings::set('slidersOrder2Direction', $title);
        }

        $this->redirectToSliders();
    }

    protected function actionExportAll() {
        $slidersModel = new ModelSliders($this);
        $sliders      = $slidersModel->getAll(Request::$REQUEST->getInt('currentGroupID', 0), 'published');

        $ids = Request::$REQUEST->getVar('sliders');

        $files = array();
        foreach ($sliders as $slider) {
            if (!empty($ids) && !in_array($slider['id'], $ids)) {
                continue;
            }
            $export  = new ExportSlider($this, $slider['id']);
            $files[] = $export->create(true);
        }

        $zip = new Creator();
        foreach ($files as $file) {
            $zip->addFile(file_get_contents($file), basename($file));
            unlink($file);
        }
        PageFlow::cleanOutputBuffers();
        header('Content-disposition: attachment; filename=sliders_unzip_to_import.zip');
        header('Content-type: application/zip');
        echo $zip->file();
        PageFlow::exitApplication();
    
    }

    protected function actionImport() {
        if ($this->validatePermission('smartslider_edit')) {

            $groupID = Request::$REQUEST->getVar('groupID', 0);

            $view = new ViewSlidersImport($this);
            $view->setGroupID($groupID);
            $view->display();
        }
    
    }
}