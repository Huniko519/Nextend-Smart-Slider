<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Dribbble;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Misc\OAuth\OAuth;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\Framework\Router\Router;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Elements\DribbbleToken;

class ConfigurationDribbble extends AbstractGeneratorGroupConfiguration {

    /** @var Data */
    private $data;

    /**
     * @param GeneratorGroupDribbble $group
     */
    public function __construct($group) {
        parent::__construct($group);
        $this->data = new Data(array(
            'apiKey'      => '',
            'apiSecret'   => '',
            'accessToken' => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('dribbble'));

    }

    public function wellConfigured() {
        if (!$this->data->get('apiKey') || !$this->data->get('apiSecret') || !$this->data->get('accessToken')) {
            return false;
        }

        $client = $this->getApi();

        try {
            $success = $client->CallAPI('https://api.dribbble.com/v2/user', 'GET', array(), array('FailOnAccessError' => true), $user);

            if (!$success) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getApi() {

        $client = new OAuth();

        $client->server = 'Dribbble';

        $client->client_id     = $this->data->get('apiKey');
        $client->client_secret = $this->data->get('apiSecret');
        $client->access_token  = $this->data->get('accessToken', null);
        $client->scope         = 'public';
        $client->debug         = true;
        $client->debug_http    = true;

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('dribbble', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {

        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());
        new Token($form->getFieldsetHidden());


        $table = new ContainerTable($form->getContainer(), 'dribbble', 'Dribbble api');

        $instruction     = $table->createRow('dribbble-instruction');
        $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'Dribbble', '<a href="https://smartslider.helpscoutdocs.com/article/1909-dribbble-generator" target="_blank">', '</a>');
        new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);

        $row1 = $table->createRow('dribbble-1');

        new Text($row1, 'apiKey', 'Client ID', '', array(
            'style' => 'width:450px;'
        ));
        new Text($row1, 'apiSecret', 'Client secret', '', array(
            'style' => 'width:450px;'
        ));
        new DribbbleToken($row1, 'accessToken', n2_('Token'), '', array(
            'style' => 'width:450px;'
        ));
        new Notice($row1, 'callback', n2_('Callback url'), $this->getCallbackUrl($MVCHelper->getRouter()));


        echo $form->render();

        try {
            $this->getApi();
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }
    }

    public function startAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(Request::$REQUEST->getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        $client               = $this->getApi();
        $client->redirect_uri = $MVCHelper->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => Request::$REQUEST->getVar('group')
            )
        ));

        $client->Initialize();
        if (isset($_SESSION['OAUTH_ACCESS_TOKEN'])) unset($_SESSION['OAUTH_ACCESS_TOKEN']);
        if (isset($_SESSION['OAUTH_STATE'])) unset($_SESSION['OAUTH_STATE']);
        $client->access_token = '';
        $client->CheckAccessToken($redirect_uri);

        return $redirect_uri;
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $client               = $this->getApi();
            $client->redirect_uri = $MVCHelper->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => Request::$REQUEST->getVar('group')
                )
            ));
            $client->Initialize();
            $client->CheckAccessToken($redirect_uri);
            $accessToken = $client->access_token;

            if ($accessToken) {
                $data                = $this->getData();
                $data['accessToken'] = $accessToken;
                $this->addData($data);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getProjects() {
        $userID = Request::$REQUEST->getVar('userID');
        if ($userID == '' || $userID == 'me') {

            $this->getApi()
                 ->CallAPI('https://api.dribbble.com/v2/user/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        } else {
            $this->getApi()
                 ->CallAPI('https://api.dribbble.com/v2/users/' . $userID . '/projects', 'GET', array('per_page' => 100), array('FailOnAccessError' => true), $result);
        }

        $projects = array();
        if (count($result)) {
            foreach ($result AS $project) {
                $projects[$project->id] = $project->name;
            }
        } else {
            $projects[''] = 'No public projects';
        }

        return $projects;
    }

    /**
     * @param Router $router
     *
     * @return string
     */
    private function getCallbackUrl($router) {

        return $router->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => 'dribbble'
            )
        ));
    }
}