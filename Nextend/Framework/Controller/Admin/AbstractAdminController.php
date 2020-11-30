<?php


namespace Nextend\Framework\Controller\Admin;


use Nextend\Framework\Asset\Css\Css;
use Nextend\Framework\Asset\Predefined;
use Nextend\Framework\Controller\AbstractController;
use Nextend\Framework\Settings;

abstract class AbstractAdminController extends AbstractController {

    public function initialize() {
        // Prevent browser from cache on backward button.
        header("Cache-Control: no-store");

        parent::initialize();

        Predefined::frontend();
        Predefined::backend();
    }
}