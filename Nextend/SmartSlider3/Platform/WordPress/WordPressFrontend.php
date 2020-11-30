<?php


namespace Nextend\SmartSlider3\Platform\WordPress;


use Nextend\Framework\PageFlow;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;

class WordPressFrontend {

    public function __construct() {

        add_action('init', array(
            $this,
            'preRender'
        ), 1000000);
    }

    public function preRender() {

        if (isset($_GET['n2prerender']) && isset($_GET['n2app'])) {
            if (current_user_can('smartslider') || (!empty($_GET['h']) && ($_GET['h'] === sha1(NONCE_SALT . date('Y-m-d')) || $_GET['h'] === sha1(NONCE_SALT . date('Y-m-d', time() - 60 * 60 * 24))))) {
                try {

                    $application = ApplicationSmartSlider3::getInstance();

                    $applicationType = $application->getApplicationTypeFrontend();

                    $applicationType->process('PreRender' . $_GET['n2controller'], $_GET['n2action']);

                    PageFlow::exitApplication();
                } catch (\Exception $e) {
                    exit;
                }
            } else if (isset($_GET['sliderid']) && isset($_GET['hash']) && md5($_GET['sliderid'] . NONCE_SALT) == $_GET['hash']) {
                try {
                    $application = ApplicationSmartSlider3::getInstance();

                    $applicationType = $application->getApplicationTypeFrontend();

                    $applicationType->process('PreRenderSlider', 'iframe');

                    PageFlow::exitApplication();
                } catch (\Exception $e) {
                    exit;
                }
            }
        }
    }
}