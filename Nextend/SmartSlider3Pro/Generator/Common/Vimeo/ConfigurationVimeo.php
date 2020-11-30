<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Vimeo;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\Framework\Router\Router;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Elements\VimeoToken;
use Vimeo\Vimeo;

class ConfigurationVimeo extends AbstractGeneratorGroupConfiguration {

    private $data;

    /**
     * N2SliderGeneratorVimeoConfiguration constructor.
     *
     * @param GeneratorGroupVimeo $group
     */
    public function __construct($group) {
        parent::__construct($group);
        $this->data = new Data(array(
            'client_id'     => '',
            'client_secret' => '',
            'access_token'  => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('vimeo'));

    }

    public function wellConfigured() {
        if (!$this->data->get('client_id') || !$this->data->get('client_secret') || !$this->data->get('access_token')) {
            return false;
        }
        $client = $this->getApi();

        $response = $client->request('/oauth/verify');
        if ($response['status'] == 200) {
            return true;
        }

        return false;
    }

    /**
     *
     * @return Vimeo
     */
    public function getApi() {

        require_once(dirname(__FILE__) . "/api/Exceptions/ExceptionInterface.php");
        require_once(dirname(__FILE__) . "/api/Exceptions/VimeoRequestException.php");
        require_once(dirname(__FILE__) . "/api/Exceptions/VimeoUploadException.php");
        require_once(dirname(__FILE__) . "/api/Vimeo.php");

        $client = new Vimeo($this->data->get('client_id'), $this->data->get('client_secret'));

        $client->clientCredentials('private');

        $client->setToken($this->data->get('access_token'));

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('vimeo', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {
        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());

        $table = new ContainerTable($form->getContainer(), 'vimeo-generator', 'Vimeo api');

        $instruction     = $table->createRow('vimeo-instruction');
        $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'Vimeo', '<a href="https://smartslider.helpscoutdocs.com/article/1912-vimeo-generator" target="_blank">', '</a>');
        new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);

        $settings = $table->createRow('vimeo');
        new Text($settings, 'client_id', 'Client identifier', '', array(
            'style' => 'width:400px;'
        ));
        new Text($settings, 'client_secret', 'Client secret', '', array(
            'style' => 'width:400px;'
        ));
        new VimeoToken($settings, 'access_token', n2_('Token'));
        new Notice($settings, 'callback', n2_('Callback url'), $this->getCallbackUrl($MVCHelper->getRouter()));
        new Token($settings);

        echo $form->render();

        if ($this->data->get('client_id') != '' && $this->data->get('client_secret') != '') {
            try {
                $client = $this->getApi();

                $response = $client->request('/oauth/verify');
                if ($response['status'] != 200) {
                    if (!empty($response['body']['error'])) {
                        Notification::error($response['body']['error']);
                    }
                }
            } catch (\Exception $e) {
                Notification::error($e->getMessage());
            }
        }
    }

    public function startAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(Request::$REQUEST->getVar('generator'), false);

        $_SESSION['data']       = $this->getData();
        $_SESSION['vimeostate'] = rand();

        $client = $this->getApi();

        $FinishAuthUrl = $MVCHelper->createUrl(array(
            "generator/finishAuth",
            array(
                'group' => 'vimeo'
            )
        ));

        return $client->buildAuthorizationEndpoint($FinishAuthUrl, array('private'), $_SESSION['vimeostate']);
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        if (isset($_REQUEST['state']) && isset($_SESSION['vimeostate']) && $_REQUEST['state'] == $_SESSION['vimeostate']) {
            $this->addData($_SESSION['data'], false);
            unset($_SESSION['data']);
            unset($_SESSION['vimeostate']);
            try {
                $client = $this->getApi();
                $client->setToken('');
                $FinishAuthUrl = $MVCHelper->createUrl(array(
                    "generator/finishAuth",
                    array(
                        'group' => 'vimeo'
                    )
                ));

                $response = $client->accessToken($_REQUEST['code'], $FinishAuthUrl);

                if ($response['status'] == 200) {
                    $this->data->set('access_token', $response['body']['access_token']);
                    $client->setToken($response['body']['access_token']);
                    $this->addData($this->getData());

                    return true;
                } else {
                    return $client->response['body']['error_description'];
                }
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            return 'State does not match!';
        }
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
                'group' => 'vimeo'
            )
        ));
    }
}