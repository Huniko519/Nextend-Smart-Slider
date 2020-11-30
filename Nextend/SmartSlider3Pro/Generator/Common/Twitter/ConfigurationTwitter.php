<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Twitter;

use Exception;
use JURI;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Hidden;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\Twitter\Elements\TwitterToken;
use NTmhOAuth;
use SimpleXMLElement;

class ConfigurationTwitter extends AbstractGeneratorGroupConfiguration {

    private $data;

    /**
     * ConfigurationTwitter constructor.
     *
     * @param GeneratorGroupTwitter $generatorGroup
     */
    public function __construct($generatorGroup) {
        parent::__construct($generatorGroup);

        $this->data = new Data(array(
            'consumer_key'    => '',
            'consumer_secret' => '',
            'user_token'      => '',
            'user_secret'     => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('twitter'));

    }

    public function wellConfigured() {
        if (!$this->data->get('consumer_key') || !$this->data->get('consumer_secret') || !$this->data->get('user_token') || !$this->data->get('user_secret')) {
            return false;
        }
        $client       = $this->getApi();
        $responseCode = $client->request('GET', $client->url('1.1/account/verify_credentials'));
        if ($responseCode == 200) {
            return true;
        }

        return false;
    }

    public function getApi($hasUser = true) {

        require_once(dirname(__FILE__) . "/api/tmhOAuth.php");
        $config = array(
            'consumer_key'    => $this->data->get('consumer_key'),
            'consumer_secret' => $this->data->get('consumer_secret')
        );
        if ($hasUser) {
            $config['token']  = $this->data->get('user_token');
            $config['secret'] = $this->data->get('user_secret');
        }
        $client = new NTmhOAuth($config);

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('twitter', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {

        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());

        $table = new ContainerTable($form->getContainer(), 'twitter-generator', 'Twitter api');

        $instruction     = $table->createRow('twitter-instruction');
        $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'Twitter', '<a href="https://smartslider.helpscoutdocs.com/article/1907-twitter-generator" target="_blank">', '</a>');
        new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);

        $settings = $table->createRow('twitter');
        new Text($settings, 'consumer_key', 'Consumer key', '', array(
            'style' => 'width:400px;'
        ));
        new Text($settings, 'consumer_secret', 'Consumer secret', '', array(
            'style' => 'width:400px;'
        ));
        new TwitterToken($settings, 'user_token', n2_('Token'));
        new Hidden($settings, 'user_secret');

        new Notice($settings, 'callback', n2_('Callback url'), $this->getCallbackUrl());
        new Token($settings);

        echo $form->render();

        if ($this->data->get('consumer_key') != '' && $this->data->get('consumer_secret') != '') {
            try {
                $client       = $this->getApi();
                $responseCode = $client->request('GET', $client->url('1.1/account/verify_credentials'));

                if ($responseCode != 200) {
                    $response = json_decode($client->response['response'], true);
                    if (!empty($response['errors'])) {
                        foreach ($response['errors'] AS $error) {
                            Notification::error($error['message']);
                        }
                    }
                }
            } catch (Exception $e) {
                Notification::error($e->getMessage());
            }
        }
    }

    public function startAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(Request::$REQUEST->getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        $client = $this->getApi(false);
        $code   = $client->request('POST', $client->url('oauth/request_token', ''), array(
            'oauth_callback' => $MVCHelper->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => 'twitter'
                )
            ))
        ));
        if ($code == 200) {
            $oauth               = $client->extract_params($client->response['response']);
            $_SESSION['t_oauth'] = $oauth;

            $a = $client->url("oauth/authenticate", '') . "?oauth_token=" . $oauth['oauth_token'] . "&force_login=1";

            return $a;
        } else {
            if (!empty($client->response['response']) && $client->response['response'][0] == '<') {
                $xml = new SimpleXMLElement($client->response['response']);
                if (isset($xml->error)) {
                    throw new Exception((string)$xml->error);
                }

            }

            $response = json_decode($client->response['response'], true);
            throw new Exception($response['errors'][0]['message']);
        }
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $this->data->set('user_token', $_SESSION['t_oauth']['oauth_token']);
            $this->data->set('user_secret', $_SESSION['t_oauth']['oauth_token_secret']);
            $client = $this->getApi();
            $code   = $client->request('POST', $client->url('oauth/access_token', ''), array(
                'oauth_verifier' => $_REQUEST['oauth_verifier']
            ));

            if ($code == 200) {
                $access_token = $client->extract_params($client->response['response']);
                unset($_SESSION['data']);
                unset($_SESSION['t_oauth']);
                $this->data->set('user_token', $access_token['oauth_token']);
                $this->data->set('user_secret', $access_token['oauth_token_secret']);
                $this->addData($this->getData());

                return true;
            } else {
                return $client->response['response'];
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    private function getCallbackUrl() {
        $admin_url = admin_url('admin.php');

        return $admin_url;
    }

}