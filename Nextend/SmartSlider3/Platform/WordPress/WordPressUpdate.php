<?php


namespace Nextend\SmartSlider3\Platform\WordPress;


use Exception;
use Nextend\Framework\Pattern\SingletonTrait;
use Nextend\SmartSlider3\Application\Model\ModelLicense;
use Nextend\SmartSlider3\SmartSlider3Info;
use WP_Error;

class WordPressUpdate {

    use SingletonTrait;

    protected function init() {

        add_filter('plugins_api', array(
            $this,
            'plugins_api'
        ), 20, 3); // WooCommerce use priority 20, so better to follow

        add_filter('pre_set_site_transient_update_plugins', array(
            $this,
            'injectUpdate'
        ));

        add_filter('upgrader_pre_download', array(
            $this,
            'upgrader_pre_download'
        ), 10, 3);
    }

    public static function plugins_api($res, $action, $args) {
        if ($action === 'plugin_information' && $args->slug === NEXTEND_SMARTSLIDER_3_SLUG) {
            try {
                $a            = (array)$args;
                $a['action']  = $action;
                $a['channel'] = SmartSlider3Info::$channel;
                $response     = SmartSlider3Info::api($a);

                $res = (object)$response['data'];
            } catch (Exception $e) {
                $res = new WP_Error('error', $e->getMessage());
            }
        }

        return $res;
    }

    public static function injectUpdate($transient) {

        $filename = NEXTEND_SMARTSLIDER_3_BASENAME;

        if (!isset($transient->response[$filename])) {

            try {
                $response = SmartSlider3Info::api(array(
                    'action'  => 'plugin_information',
                    'slug'    => NEXTEND_SMARTSLIDER_3_SLUG,
                    'channel' => SmartSlider3Info::$channel
                ));
                if ($response['status'] == 'OK') {
                    $item = (object)$response['data'];
                } else {
                    throw new Exception();
                }
            } catch (Exception $e) {
                $item = new WP_Error('error', $e->getMessage());
            }

            if (!is_wp_error($item)) {

                $updateLink = SmartSlider3Info::api(array(
                    'action'  => 'update',
                    'channel' => SmartSlider3Info::$channel
                ), true);

                $item->package                  = $updateLink;
                $item->download_link            = $updateLink;
                $item->versions                 = array();
                $item->versions[$item->version] = $updateLink;

                if (version_compare(SmartSlider3Info::$version, $item->version, '<')) {
                    $transient->response[$filename] = (object)$item;
                    unset($transient->no_update[$filename]);
                } else {
                    $transient->no_update[$filename] = (object)$item;
                    unset($transient->response[$filename]);
                }


            }
        }

        return $transient;
    }

    public function upgrader_pre_download($reply, $package, $upgrader) {
        if (strpos($package, 'product=smartslider3') === false) {
            return $reply;
        }

        $status = ModelLicense::getInstance()
                              ->isActive(false);

        $message = '';
        switch ($status) {
            case 'OK':
                return $reply;
            case 'ASSET_PREMIUM':
            case 'LICENSE_EXPIRED':
                $message = 'Your <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">license</a> has expired! Get new one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>.';
                break;
            case 'DOMAIN_REGISTER_FAILED':
                $message = 'Smart Slider 3 Pro license is not registered on the current domain. Please activate this domain by following <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">the license activation documentation</a>.';
                break;
            case 'LICENSE_INVALID':
                $message = 'Smart Slider 3 Pro license is not registered on the current domain. Please activate this domain by following <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">the license activation documentation</a>.';
                ModelLicense::getInstance()
                            ->setKey('');
                break;
            case 'PLATFORM_NOT_ALLOWED':
                $message = 'Your <a href="https://smartslider.helpscoutdocs.com/article/1718-activation" target="_blank">license</a> is not valid for WordPress! Get a license for WordPress: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>';
                break;
            case '503':
                $message = 'Licensing server is down, try again later!';
                break;
            case null:
                $message = 'Licensing server not reachable, try again later!';
                break;
            default:
                $message = 'Unknown error, please write an email to support@nextendweb.com with the following status: ' . $status;
                break;
        }

        $reply                  = new WP_Error('SS3_ERROR', $message);
        $upgrader->result       = null;
        $upgrader->skin->result = $reply;

        return $reply;
    }
}