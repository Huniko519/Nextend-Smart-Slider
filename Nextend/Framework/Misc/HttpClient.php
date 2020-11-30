<?php

namespace Nextend\Framework\Misc;

use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Settings;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;

class HttpClient {

    public static function getCacertPath() {
        return dirname(__FILE__) . '/cacert.pem';
    }

    public static function get($url, $options = array()) {

        $options = array_merge(array(
            'referer' => $_SERVER['REQUEST_URI']
        ), $options);

        if (function_exists('curl_init') && function_exists('curl_exec') && Settings::get('curl', 1)) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/73.0.3683.103 Safari/537.36');
            $proxy = new \WP_HTTP_Proxy();

            if ($proxy->is_enabled() && $proxy->send_through_proxy($url)) {


                curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

                curl_setopt($ch, CURLOPT_PROXY, $proxy->host());

                curl_setopt($ch, CURLOPT_PROXYPORT, $proxy->port());


                if ($proxy->use_authentication()) {

                    curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);

                    curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy->authentication());
                }
            }
        

            if (!empty($options['referer'])) {
                curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
            }


            if (Settings::get('curl-clean-proxy', 0)) {
                curl_setopt($ch, CURLOPT_PROXY, '');
            }

            $data = curl_exec($ch);
            if (curl_errno($ch) == 60) {
                curl_setopt($ch, CURLOPT_CAINFO, self::getCacertPath());
                $data = curl_exec($ch);
            }
            $error           = curl_error($ch);
            $curlErrorNumber = curl_errno($ch);
            curl_close($ch);

            if ($curlErrorNumber) {


                $href = ApplicationSmartSlider3::getInstance()
                                               ->getApplicationTypeAdmin()
                                               ->getUrlHelpCurl();

                if (!isset($options['error']) || $options['error'] !== false) {
                    Notification::error(Html::tag('a', array(
                        'href' => $href . '#support-form'
                    ), n2_('Debug error')));

                    Notification::error($curlErrorNumber . $error);
                }

                return false;
            }

            return $data;
        } else {

            if (!ini_get('allow_url_fopen')) {
                Notification::error(sprintf(n2_('The %1$s is not turned on in your server, which is necessary to read rss feeds. You should contact your server host, and ask them to enable it!'), '<a href="http://php.net/manual/en/filesystem.configuration.php#ini.allow-url-fopen" target="_blank">allow_url_fopen</a>'));

                return false;
            }

            $opts    = array(
                'http' => array(
                    'method' => 'GET'
                )
            );
            $context = stream_context_create($opts);
            $data    = file_get_contents($url, false, $context);
            if ($data === false) {
                Notification::error(n2_('CURL disabled in your php.ini configuration. Please enable it!'));

                return false;
            }
            $headers = self::parseHeaders($http_response_header);
            if ($headers['status'] != '200') {
                Notification::error(n2_('Unable to contact with the licensing server, please try again later!'));

                return false;
            }

            return $data;
        }
    }

    private static function parseHeaders(array $headers, $header = null) {
        $output = array();
        if ('HTTP' === substr($headers[0], 0, 4)) {
            list(, $output['status'], $output['status_text']) = explode(' ', $headers[0]);
            unset($headers[0]);
        }
        foreach ($headers as $v) {
            $h = preg_split('/:\s*/', $v);
            if (count($h) >= 2) {
                $output[strtolower($h[0])] = $h[1];
            }
        }
        if (null !== $header) {
            if (isset($output[strtolower($header)])) {
                return $output[strtolower($header)];
            }

            return null;
        }

        return $output;
    }
}