<?php


namespace Nextend\Framework;


use Exception;
use JHttp;
use Nextend\Framework\Misc\Base64;
use Nextend\Framework\Misc\HttpClient;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\Url\Url;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Application\ApplicationSmartSlider3;

class Api {

    private static $api = 'https://api.nextendweb.com/v1/';

    public static function getApiUrl() {

        return self::$api;
    }

    public static function api($posts, $returnUrl = false) {

        $api = self::getApiUrl();

        $posts_default = array(
            'platform' => Platform::getName()
        );
        $posts_default['domain'] = parse_url(Url::getSiteUri(), PHP_URL_HOST);
    

        $posts = $posts + $posts_default;

        if ($returnUrl) {
            return $api . '?' . http_build_query($posts, '', '&');
        }

        if (!isset($data)) {
            if (function_exists('curl_init') && function_exists('curl_exec') && Settings::get('curl', 1)) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api);

                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($posts, '', '&'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 20);
                curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
                curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
                $proxy = new \WP_HTTP_Proxy();

                if ($proxy->is_enabled() && $proxy->send_through_proxy($api)) {

                    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

                    curl_setopt($ch, CURLOPT_PROXY, $proxy->host());

                    curl_setopt($ch, CURLOPT_PROXYPORT, $proxy->port());


                    if ($proxy->use_authentication()) {

                        curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_ANY);

                        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy->authentication());
                    }
                }
            

                if (Settings::get('curl-clean-proxy', 0)) {
                    curl_setopt($ch, CURLOPT_PROXY, '');
                }
                $data        = curl_exec($ch);
                $errorNumber = curl_errno($ch);
                if ($errorNumber == 60 || $errorNumber == 77) {
                    curl_setopt($ch, CURLOPT_CAINFO, HttpClient::getCacertPath());
                    $data = curl_exec($ch);
                }
                $contentType     = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                $error           = curl_error($ch);
                $curlErrorNumber = curl_errno($ch);
                curl_close($ch);

                if ($curlErrorNumber) {
                    $href = ApplicationSmartSlider3::getInstance()
                                                   ->getApplicationTypeAdmin()
                                                   ->getUrlHelpCurl();
                    Notification::error(Html::tag('a', array(
                        'href' => $href . '#support-form'
                    ), n2_('Debug error')));

                    Notification::error($curlErrorNumber . $error);

                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
            } else {
                $opts    = array(
                    'http' => array(
                        'method'  => 'POST',
                        'header'  => 'Content-type: application/x-www-form-urlencoded',
                        'content' => http_build_query($posts, '', '&')
                    )
                );
                $context = stream_context_create($opts);
                $data    = file_get_contents($api, false, $context);
                if ($data === false) {
                    Notification::error(n2_('CURL disabled in your php.ini configuration. Please enable it!'));

                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
                $headers = self::parseHeaders($http_response_header);
                if ($headers['status'] != '200') {
                    Notification::error(n2_('Unable to contact with the licensing server, please try again later!'));

                    return array(
                        'status' => 'ERROR_HANDLED'
                    );
                }
                if (isset($headers['content-type'])) {
                    $contentType = $headers['content-type'];
                }
            }
        }

        switch ($contentType) {
            case 'text/html; charset=UTF-8':

                Notification::error(sprintf('Unexpected response from the API.<br>Contact us (support@nextendweb.com) with the following log:') . '<br><textarea style="width: 100%;height:200px;font-size:8px;">' . Base64::encode($data) . '</textarea>');

                return array(
                    'status' => 'ERROR_HANDLED'
                );
                break;
            case 'application/json':
                return json_decode($data, true);
        }

        return $data;
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