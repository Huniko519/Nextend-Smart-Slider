<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\Framework\Router\Router;
use Nextend\GoogleApi\Google_Service_YouTube_PlaylistListResponse;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\Elements\YouTubeToken;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Google_Client;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Service\Google_Service_YouTube;

class ConfigurationYoutube extends AbstractGeneratorGroupConfiguration {

    private $data;

    /**
     * N2SSPluginGeneratorYoutube constructor.
     *
     * @param GeneratorGroupYouTube $group
     */
    public function __construct($group) {
        parent::__construct($group);

        $this->data = new Data(array(
            'apiKey'      => '',
            'apiSecret'   => '',
            'accessToken' => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('youtube'));

    }

    public function wellConfigured() {
        if (!$this->data->get('apiKey') || !$this->data->get('apiSecret') || !$this->data->get('accessToken')) {
            return false;
        }

        $api = $this->getApi();
        try {
            if ($api->isAccessTokenExpired()) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getApi() {

        $client = new Google_Client();
        $client->setAccessType('offline');

        $client->setClientId(trim($this->data->get('apiKey')));
        $client->setClientSecret(trim($this->data->get('apiSecret')));
        $client->addScope(array(
            Google_Service_YouTube::YOUTUBE,
            Google_Service_YouTube::YOUTUBE_READONLY
        ));


        $client->setRedirectUri(ApplicationSmartSlider3::getInstance()
                                                       ->getApplicationTypeAdmin()
                                                       ->createUrl(array(
                                                           "generator/finishAuth",
                                                           array(
                                                               'group' => Request::$REQUEST->getVar('group')
                                                           )
                                                       )));

        $token = Base64::decode($this->data->get('accessToken', null));
        try {
            if ($token) {
                $client->setAccessToken($token);
                if ($client->isAccessTokenExpired()) {
                    $refreshToken = $client->getRefreshToken();
                    if (!empty($refreshToken)) {
                        $client->refreshToken($refreshToken);

                        try {
                            $oldAccessToken = json_decode(Base64::decode($this->data->get('accessToken')), true);
                            if (!is_array($oldAccessToken)) {
                                $oldAccessToken = array();
                            }
                        } catch (\Exception $e) {
                            $oldAccessToken = array();
                        }

                        $this->data->set('accessToken', Base64::encode(json_encode(array_merge($oldAccessToken, json_decode($client->getAccessToken(), true)))));
                        $this->addData($this->data->toArray());
                    }
                }
            }
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }

        return $client;
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('youtube', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {
        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());

        $table = new ContainerTable($form->getContainer(), 'youtube-generator', 'YouTube api');

        $instruction     = $table->createRow('youtube-instruction');
        $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'YouTube', '<a href="https://smartslider.helpscoutdocs.com/article/1906-youtube-generator" target="_blank">', '</a>');
        new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);

        $settings = $table->createRow('youtube');
        new Text($settings, 'apiKey', 'Client ID', '', array(
            'style' => 'width:600px;'
        ));
        new Text($settings, 'apiSecret', 'Client secret', '', array(
            'style' => 'width:250px;'
        ));
        new YoutubeToken($settings, 'accessToken', n2_('Token'));
        new Notice($settings, 'callback', n2_('Callback url'), $this->getCallbackUrl($MVCHelper->getRouter()));
        new Token($settings);

        echo $form->render();

        try {
            $this->getApi();
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }
    }

    public function startAuth($approvalPrompt = 'auto') {
        if (session_id() == "") {
            session_start();
        }
        $this->addData(Request::$REQUEST->getVar('generator'), false);

        $_SESSION['data'] = $this->getData();

        $client = $this->getApi();
        $client->setApprovalPrompt($approvalPrompt);
        $client->setAccessType('offline');

        return $client->createAuthUrl();
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);
        try {
            $client = $this->getApi();
            $client->authenticate($_GET['code']);
            $accessToken = $client->getAccessToken();

            if ($accessToken) {
                $data = $this->getData();

                try {
                    $oldAccessToken = json_decode(Base64::decode($data['accessToken']), true);
                    if (!is_array($oldAccessToken)) {
                        $oldAccessToken = array();
                    }
                } catch (\Exception $e) {
                    $oldAccessToken = array();
                }

                $newAccessToken = array_merge($oldAccessToken, json_decode($accessToken, true));

                if (!isset($newAccessToken['refresh_token'])) {
                    header('Location: ' . $this->startAuth('force'));
                    exit;
                }

                $data['accessToken'] = Base64::encode(json_encode($newAccessToken));
                $this->addData($data);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getPlayListsAjax() {
        $channelID = Request::$REQUEST->getVar('channelID');

        $api = $this->getApi();

        $playLists = $this->getPlaylists($api, $channelID);


        $response = array();
        if (count($playLists)) {
            foreach ($playLists AS $playlist) {
                $response[$playlist['id']] = $playlist['snippet']['title'];
            }
        }

        return $response;
    }


    public function getPlaylists($api, $channelID) {
        $channelID     = trim($channelID);
        $youtubeClient = new Google_Service_YouTube($api);
        $request       = array(
            'mine'       => true,
            'maxResults' => 50
        );
        if (!empty($channelID)) {
            $request = array(
                'channelId'  => $channelID,
                'maxResults' => 50
            );
        }

        /** @var Google_Service_YouTube_PlaylistListResponse $playlists */
        $playlists = $youtubeClient->playlists->listPlaylists('id,snippet', $request);
        $items     = $playlists['items'];

        while ($nextPageToken = $playlists->getNextPageToken()) {
            $request['pageToken'] = $nextPageToken;
            /** @var Google_Service_YouTube_PlaylistListResponse $playlists */
            $playlists = $youtubeClient->playlists->listPlaylists('id,snippet', $request);
            $items     = array_merge($items, $playlists['items']);
        }

        return $items;

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
                'group' => 'youtube'
            )
        ));
    }
}