<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Facebook;

use Facebook\Authentication\AccessToken;
use Facebook\Facebook;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Message\Notice;
use Nextend\Framework\Form\Element\Message\Warning;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Token;
use Nextend\Framework\Form\Form;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;
use Nextend\Framework\Router\Router;
use Nextend\SmartSlider3\Generator\AbstractGeneratorGroupConfiguration;
use Nextend\SmartSlider3Pro\Generator\Common\Facebook\Elements\FacebookToken;

class ConfigurationFacebook extends AbstractGeneratorGroupConfiguration {

    private $data;

    /**
     * N2SliderGeneratorFacebookConfiguration constructor.
     *
     * @param GeneratorGroupFacebook $group
     */
    public function __construct($group) {
        parent::__construct($group);
        $this->data = new Data(array(
            'appId'       => '',
            'secret'      => '',
            'accessToken' => ''
        ));

        $this->data->loadJSON(StorageSectionManager::getStorage('smartslider')
                                                   ->get('facebook'));

    }

    public function wellConfigured() {
        if (!$this->data->get('appId') || !$this->data->get('secret') || !$this->data->get('accessToken')) {
            return false;
        }
        $fb = $this->getApi();
        try {
            $fb->get('/me');

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return Facebook|null
     */
    public function getApi() {

        if (!class_exists('Facebook')) {
            require(dirname(__FILE__) . '/Facebook/autoload.php');
        }
        $appId = $this->data->get('appId');
        if (!empty($appId) && !empty($this->data->get('secret'))) {
            $fb = new Facebook(array(
                'app_id'     => $this->data->get('appId'),
                'app_secret' => $this->data->get('secret')
            ));

            $accessToken = $this->data->get('accessToken');
            if ($accessToken) {
                $accessToken = json_decode($accessToken);
                if (count($accessToken) == 2) {
                    $fb->setDefaultAccessToken(new AccessToken($accessToken[0], $accessToken[1]));
                }
            }

            return $fb;
        } else if (!empty($appId) && empty($this->data->get('secret'))) {
            Notification::error(n2_('The secret is empty. Please insert that value too!'));
        } else if (empty($appId) && !empty($this->data->get('secret'))) {
            Notification::error(n2_('The App ID is empty. Please insert that value too!'));
        } else {

            return null;
        }
    }

    public function getData() {
        return $this->data->toArray();
    }

    public function addData($data, $store = true) {
        $this->data->loadArray($data);
        if ($store) {
            StorageSectionManager::getStorage('smartslider')
                                 ->set('facebook', null, json_encode($this->data->toArray()));
        }
    }

    public function render($MVCHelper) {

        $form = new Form($MVCHelper, 'generator');
        $form->loadArray($this->getData());

        $table = new ContainerTable($form->getContainer(), 'facebook-generator', 'Facebook api');

        $callBackUrl = $this->getCallbackUrl($MVCHelper->getRouter());

        if (substr($callBackUrl, 0, 8) !== 'https://') {
            $warning     = $table->createRow('facebook-warning');
            $warningText = sprintf(n2_('%1$s allows HTTPS Redirect URIs only! You must move your site to HTTPS in order to use this generator! - %2$s How to get SSL for my WordPress site? %3$s'), 'Facebook', '<a href="https://www.wpbeginner.com/wp-tutorials/how-to-add-ssl-and-https-in-wordpress/" target="_blank">', '</a>');
            new Warning($warning, 'warning', $warningText);
        } else {
            $instruction     = $table->createRow('facebook-instruction');
            $instructionText = sprintf(n2_('%2$s Check the documentation %3$s to learn how to configure your %1$s app.'), 'Facebook', '<a href="https://smartslider.helpscoutdocs.com/article/1902-facebook-generator" target="_blank">', '</a>');
            new Notice($instruction, 'instruction', n2_('Instruction'), $instructionText);
        }

        $settings = $table->createRow('facebook');
        new Text($settings, 'appId', 'App ID', '', array(
            'style' => 'width:120px;'
        ));
        new Text($settings, 'secret', 'Secret', '', array(
            'style' => 'width:250px;'
        ));

        new OnOff($settings, 'pages_read_engagement', n2_x('pages read engagement', "Facebook app permission"), 1, array(
            'tipLabel'       => n2_('Pages read engagement permission'),
            'tipDescription' => n2_('You need "pages_read_engagement" permission if you want to access datas of Facebook pages, where you are an administrator. For other pages you still need to turn this option on, but request access to "Page Public Content Access" within your App.')
        ));
        new OnOff($settings, 'user_photos', n2_x('user photos', "Facebook app permission"), 1, array(
            'tipLabel'       => n2_('User photos permission'),
            'tipDescription' => n2_('You need "user_photos" permission to access photos of users, except your own user.')
        ));

        new FacebookToken($settings, 'accessToken', n2_('Token'));
        new Notice($settings, 'callback', n2_('Callback url'), $callBackUrl);

        new Token($settings);

        echo $form->render();

        $fb = $this->getApi();
        if (!empty($fb)) {
            $accessToken = $fb->getDefaultAccessToken();
        }
        if (!empty($accessToken)) {
            try {
                /** @var Facebook\Authentication\AccessTokenMetadata $result */
                $result = $fb->getOAuth2Client()
                             ->debugToken($accessToken);

                if (!is_object($result)) {
                    Notification::error(n2_($result));
                } else if ($result->getIsValid()) {
                    $result->validateExpiration();
                    Notification::notice('The token will expire on ' . date('F j, Y', $result->getExpiresAt()
                                                                                             ->getTimestamp()));
                } else {
                    Notification::error(n2_('The token expired. Please request new token! '));
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

        $_SESSION['data'] = $this->getData();

        $permissions = array();
        if ($_SESSION['data']['pages_read_engagement']) {
            $permissions[] = 'pages_read_engagement';
        }
        if ($_SESSION['data']['user_photos']) {
            $permissions[] = 'user_photos';
        }

        $api = $this->getApi();
        if ($api) {
            return $api->getRedirectLoginHelper()
                       ->getLoginUrl($MVCHelper->createUrl(array(
                           "generator/finishAuth",
                           array(
                               'group' => Request::$REQUEST->getVar('group')
                           )
                       )), $permissions);
        }
        throw new Exception('App ID missing!');
    }

    public function finishAuth($MVCHelper) {
        if (session_id() == "") {
            session_start();
        }
        $this->addData($_SESSION['data'], false);
        unset($_SESSION['data']);

        $fb = $this->getApi();
        try {
            $helper      = $fb->getRedirectLoginHelper();
            $accessToken = $helper->getAccessToken($MVCHelper->createUrl(array(
                "generator/finishAuth",
                array(
                    'group' => Request::$REQUEST->getVar('group')
                )
            )));

            if (!isset($accessToken)) {
                echo 'Access token was not returned from Graph.';
                exit;
            }

        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                // The OAuth 2.0 client handler helps us manage access tokens
                $oAuth2Client = $fb->getOAuth2Client();
                $accessToken  = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (\Facebook\Exceptions\FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        $fb->setDefaultAccessToken($accessToken);

        try {
            $user = $fb->get('/me');
            if ($user) {
                $data                = $this->getData();
                $data['accessToken'] = json_encode(array(
                    $accessToken->getValue(),
                    $accessToken->getExpiresAt()
                                ->getTimestamp()
                ));
                $this->addData($data);

                return true;
            }

            return false;
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function getAlbums() {
        $ID = Request::$REQUEST->getVar('facebookID');

        $api        = $this->getApi();
        $apiRequest = $api->sendRequest('GET', $ID . '/albums');
        if (is_object($apiRequest)) {
            $result = $apiRequest->getDecodedBody();

            $albums = array();
            if (count($result['data'])) {
                foreach ($result['data'] AS $album) {
                    $albums[$album['id']] = $album['name'];
                }
            }

            return $albums;
        } else {
            Notification::error($apiRequest['response_error']);

            return false;
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
                'group' => Request::$REQUEST->getVar('group')
            )
        ));
    }

}