<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Flickr;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\Framework\Router\Router;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\api\DPZFlickr;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Elements\FlickrToken;

class ConfigurationFlickr extends AbstractGeneratorGroupConfiguration {

    private $data;

    /**
     * N2SliderGeneratorFlickrConfiguration constructor.
     *
     * @param GeneratorGroupFlickr $group
     */
    public function __construct($group) {
        parent::__construct($group);
        $this->data = new Data(array(
            'api_key'    => '',
            'api_secret' => '',
            'token'      => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('flickr'));
    }

    public function wellConfigured() {
        if (!$this->data->get('api_key') || !$this->data->get('api_secret') || !$this->data->get('token')) {
            return false;
        }
        $api = $this->getApi();

        if ($api->call('flickr.test.login') === false) {
            return false;
        }

        return true;
    }

    public function getApi() {
        $api_key    = $this->data->get('api_key');
        $api_secret = $this->data->get('api_secret');
        $auth_url   = ApplicationSmartSlider3::getInstance()
                                             ->getApplicationTypeAdmin()
                                             ->createUrl(array(
                                                 "generator/finishAuth",
                                                 array(
                                                     'group' => Request::$REQUEST->getVar('group')
                                                 )
                                             ));

        $api = new DPZFlickr($api_key, $api_secret, $auth_url);

        $token = json_decode($this->data->get('token'), true);
        $api->setData($token);

        return $api;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('flickr', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {

        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());


        $table = new ContainerTable($form->getContainer(), 'flickr-api', 'Flickr api');

        $callBackUrl = $this->getCallbackUrl($MVCHelper->getRouter());

        if (substr($callBackUrl, 0, 8) !== 'https://') {
            $warning     = $table->createRow('flickr-warning');
            $warningText = sprintf(n2_('%1$s allows HTTPS Redirect URIs only! You must move your site to HTTPS in order to use this generator! - %2$s How to get SSL for my WordPress site? %3$s'), 'Flickr', '<a href="https://www.wpbeginner.com/wp-tutorials/how-to-add-ssl-and-https-in-wordpress/" target="_blank">', '</a>');
            new Warning($warning, 'warning', $warningText);
        } else {
            $instruction     = $table->createRow('flickr-instruction');
            $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'Flickr', '<a href="https://smartslider.helpscoutdocs.com/article/1905-flickr-generator" target="_blank">', '</a>');
            new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);
        }

        $settings = $table->createRow('flickr');
        new Text($settings, 'api_key', 'Api key', '', array(
            'style' => 'width:250px;'
        ));
        new Text($settings, 'api_secret', 'Api secret', '', array(
            'style' => 'width:250px;'
        ));
        new FlickrToken($settings, 'token', n2_('Token'));
        new Notice($settings, 'callback', n2_('Callback url'), $callBackUrl);
        new Token($settings);

        $api = $this->getApi();
        if ($api->call('flickr.test.login') === false) {
            Notification::error(n2_('The key and secret is not valid!'));
        }

        echo $form->render();
    }

    public function startAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(Request::$REQUEST->getVar('generator'), false);

        $_SESSION['data'] = $this->getData();
        $api              = $this->getApi();
        $api->setData(array());

        $url = $api->authenticate();

        if (!$url) {
            throw new Exception('Api key or Api secret is not valid.');
        }

        return $url;
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }

        $api = $this->getApi();
        $api->setData(array());
        $api->authenticateStep2();

        $this->data->loadArray($_SESSION['data']);
        $data          = $this->getData();
        $data['token'] = json_encode(array(
            'oauth_request_token'        => $api->getOauthData('oauth_request_token'),
            'oauth_request_token_secret' => $api->getOauthData('oauth_request_token_secret'),
            'oauth_access_token'         => $api->getOauthData('oauth_access_token'),
            'oauth_access_token_secret'  => $api->getOauthData('oauth_access_token_secret'),
            'user_nsid'                  => $api->getOauthData('user_nsid')
        ));

        $this->addData($data);

        unset($_SESSION['FlickrSessionOauthData']);
        unset($_SESSION['data']);

        return true;
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
                'group' => 'flickr'
            )
        ));
    }
}
