<?php


namespace Nextend\SmartSlider3;

use Nextend\Framework\Api;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\Url\Url;
use Nextend\Framework\Url\UrlHelper;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;
use Nextend\SmartSlider3\Application\Model\ModelLicense;

class SmartSlider3Info {

    public static $version = '3.4.1.14';

    public static $channel = 'stable';

    public static $revision = 'f9404e8d487b96e0ed2ced845275a25e7ced4e52';

    public static $revisionShort = 'f9404e8d';

    public static $branch = 'release-3.4.1.14';

    public static $completeVersion;

    public static $completeVersionReadable;

    public static $plan = 'pro';

    public static $product = 'smartslider3';

    public static $campaign = 'smartslider3';

    public static $source = '';

    public static $forceDesktop = false;

    public static function init() {

        self::$completeVersion = self::$version . '/b:' . self::$branch . '/r:' . self::$revision;

        self::$completeVersionReadable = self::$version . 'r' . self::$revisionShort;
    }

    public static function shouldSkipLicenseModal() {
        static $shouldSkipLicenseModal;
        if ($shouldSkipLicenseModal === null) {
            $shouldSkipLicenseModal = false;
            $shouldSkipLicenseModal = apply_filters('smartslider3_skip_license_modal', $shouldSkipLicenseModal);
        
        }

        return $shouldSkipLicenseModal;
    }

    public static function applySource(&$params) {
        static $isSourceSet = false;
        if (!$isSourceSet) {
            if (defined('SMARTSLIDER3AFFILIATE')) {
                self::$source = SMARTSLIDER3AFFILIATE;
            }
            self::$source = apply_filters('smartslider3_hoplink', self::$source);
        

            $isSourceSet = true;
        }

        if (!empty(self::$source)) {
            $params['source'] = self::$source;
        }
    }

    public static function getProUrlHome($params = array()) {
        self::applySource($params);

        return 'https://smartslider3.com/?' . http_build_query($params);
    }

    public static function getProUrlPricing($params = array()) {
        self::applySource($params);

        return 'https://smartslider3.com/pricing/?' . http_build_query($params);
    }

    public static function decorateExternalUrl($url, $params = array()) {

        self::applySource($params);

        $params['utm_campaign'] = self::$campaign;
        $params['utm_medium']   = 'smartslider-' . Platform::getName() . '-' . self::$plan;

        return UrlHelper::add_query_arg($params, $url);
    }

    public static function getWhyProUrl($params = array()) {
        self::applySource($params);

        $params['utm_campaign'] = self::$campaign;
        $params['utm_medium']   = 'smartslider-' . Platform::getName() . '-' . self::$plan;


        return 'https://smartslider3.com/features/?' . http_build_query($params);
    }

    public static function getSampleSlidesUrl($params = array()) {
        self::applySource($params);
        return 'https://smartslider3.com/slides/' . self::$version . '/pro2e4G2dR/?' . http_build_query($params);
    }

    public static function getActivationUrl($params = array()) {
        self::applySource($params);

        return 'https://secure.nextendweb.com/activate/?' . http_build_query($params);
    }

    public static function getUpdateInfo() {
        return array(
            'name'   => 'smartslider3',
            'plugin' => 'nextend-smart-slider3-pro/nextend-smart-slider3-pro.php'
        );
    }

    public static function getDomain() {
        $domain = parse_url(Url::getSiteUri(), PHP_URL_HOST);
        if (empty($domain)) {
            if (isset($_SERVER['HTTP_HOST'])) {

                $domain = $_SERVER['HTTP_HOST'];
            }
            if (empty($domain) && isset($_SERVER['SERVER_NAME'])) {

                $domain = $_SERVER['SERVER_NAME'];
            }
        }

        return $domain;
    }

    public static function api($_posts, $returnUrl = false) {
        $isPro = 1;
    
        $posts = array(
            'product' => self::$product,
            'pro'     => $isPro
        );
        $posts['version'] = self::$version;
        $posts['domain']  = self::getDomain();
        $posts['license'] = ModelLicense::getInstance()
                                        ->getKey();
    

        return Api::api($_posts + $posts, $returnUrl);
    }

    public static function hasApiError($status, $data = array()) {
        extract($data);
        switch ($status) {
            case 'OK':
                return false;
            case 'PRODUCT_ASSET_NOT_AVAILABLE':
                Notification::error(sprintf(n2_('Demo slider is not available with the following ID: %s'), $key));
                break;
            case 'ASSET_PREMIUM':
                Notification::error('Premium sliders are available in Pro version only!');
                break;
            case 'ASSET_VERSION':
                Notification::error('Please <a href="https://smartslider.helpscoutdocs.com/article/1752-update" target="_blank">update</a> your Smart Slider to the latest version to be able to import the selected sample slider!');
                break;
            case 'LICENSE_EXPIRED':
                Notification::error('Your license has <a href="https://smartslider.helpscoutdocs.com/article/1718-activation#nopackage" target="_blank">expired</a>! Get new one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>.');
                break;
            case 'DOMAIN_REGISTER_FAILED':
                Notification::error('Smart Slider 3 Pro license is not registered on the current domain. Please activate this domain by following <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">the license activation documentation</a>.');
                break;
            case 'LICENSE_INVALID':
                Notification::error('Smart Slider 3 Pro license is not registered on the current domain. Please activate this domain by following <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">the license activation documentation</a>.');
                ModelLicense::getInstance()
                            ->setKey('');

                return 'dashboard';
                break;
            case 'UPDATE_ERROR':
                Notification::error('Update error, please update manually!');
                break;
            case 'PLATFORM_NOT_ALLOWED':
                Notification::error(sprintf('Your license is not valid for Smart Slider3 - %s!', Platform::getLabel()));
                break;
            case 'ERROR_HANDLED':
                break;
            case '503':
                Notification::error('Licensing server is down. Please try again later.');
                break;
            case null:
                Notification::error('Licensing server not reachable. Please allow outgoing connection to the following ip addresses: 139.162.190.63, 172.104.28.39');
                break;
            default:
                Notification::error('Debug: ' . $status);
                Notification::error('Licensing server not reachable. Please allow outgoing connection to the following ip addresses: 139.162.190.63, 172.104.28.39');
                break;
        }

        return true;
    }

    public static function initLicense() {

        $model = ModelLicense::getInstance();

        Js::addInline("new N2Classes.License(" . json_encode(array(
                'activated'      => $model->hasKey(),
                'maybeActive'    => $model->maybeActive(),
                'activationUrl'  => self::getActivationUrl(array(
                    'utm_source'   => 'activate',
                    'utm_medium'   => 'smartslider-' . Platform::getName() . '-' . self::$plan,
                    'utm_campaign' => self::$campaign
                )),
                'activationArgs' => array(
                    'product'  => self::$product,
                    'domain'   => self::getDomain(),
                    'platform' => Platform::getName()

                ),
                'ajaxUrl'        => ApplicationSmartSlider3::getInstance()
                                                           ->getApplicationTypeAdmin()
                                                           ->getAjaxUrlLicenseAdd()
            )) . ");");
    
    }

    public static function sliderChanged() {
        do_action('smartslider3_slider_changed');
    
    }
}

SmartSlider3Info::init();