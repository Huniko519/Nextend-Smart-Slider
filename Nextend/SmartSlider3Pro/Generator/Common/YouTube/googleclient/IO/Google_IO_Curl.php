<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\IO;

use Nextend\Framework\Misc\HttpClient;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Google_Client;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Http\Google_Http_Request;

/**
 * Curl based implementation of Google_IO.
 *
 * @author Stuart Langley <slangley@google.com>
 */
class Google_IO_Curl extends Google_IO_Abstract {

    // cURL hex representation of version 7.30.0
    const NO_QUIRK_VERSION = 0x071E00;

    private $options = array();

    public function __construct(Google_Client $client) {
        if (!extension_loaded('curl')) {
            $error = 'The cURL IO handler requires the cURL extension to be enabled';
            $client->getLogger()
                   ->critical($error);
            throw new Google_IO_Exception($error);
        }

        parent::__construct($client);
    }

    /**
     * Execute an HTTP Request
     *
     * @param Google_HttpRequest $request the http request to be executed
     *
     * @return Google_HttpRequest http request with the response http code,
     * response headers and response body filled in
     * @throws Google_IO_Exception on curl or IO error
     */
    public function executeRequest(Google_Http_Request $request) {
        $curl = curl_init();

        if ($request->getPostBody()) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $request->getPostBody());
        }

        $requestHeaders = $request->getRequestHeaders();
        if ($requestHeaders && is_array($requestHeaders)) {
            $curlHeaders = array();
            foreach ($requestHeaders as $k => $v) {
                $curlHeaders[] = "$k: $v";
            }
            curl_setopt($curl, CURLOPT_HTTPHEADER, $curlHeaders);
        }
        curl_setopt($curl, CURLOPT_URL, $request->getUrl());

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $request->getRequestMethod());
        curl_setopt($curl, CURLOPT_USERAGENT, $request->getUserAgent());

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        // 1 is CURL_SSLVERSION_TLSv1, which is not always defined in PHP.
        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);

        if ($request->canGzip()) {
            curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
        }
        $proxy = new \WP_HTTP_Proxy();

        if ($proxy->is_enabled() && $proxy->send_through_proxy($request->getUrl())) {


            curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);

            curl_setopt($curl, CURLOPT_PROXY, $proxy->host());

            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy->port());


            if ($proxy->use_authentication()) {

                curl_setopt($curl, CURLOPT_PROXYAUTH, CURLAUTH_ANY);

                curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy->authentication());
            }
        }
    

        foreach ($this->options as $key => $var) {
            curl_setopt($curl, $key, $var);
        }

        if (!isset($this->options[CURLOPT_CAINFO])) {
            curl_setopt($curl, CURLOPT_CAINFO, HttpClient::getCacertPath());
        }

        $this->client->getLogger()
                     ->debug('cURL request', array(
                         'url'     => $request->getUrl(),
                         'method'  => $request->getRequestMethod(),
                         'headers' => $requestHeaders,
                         'body'    => $request->getPostBody()
                     ));

        $response = curl_exec($curl);
        if ($response === false) {
            $error = curl_error($curl);
            $code  = curl_errno($curl);
            $map   = $this->client->getClassConfig('Google_IO_Exception', 'retry_map');

            $this->client->getLogger()
                         ->error('cURL ' . $error);
            throw new Google_IO_Exception($error, $code, null, $map);
        }
        $headerSize = curl_getinfo($curl, CURLINFO_HEADER_SIZE);

        list($responseHeaders, $responseBody) = $this->parseHttpResponse($response, $headerSize);
        $responseCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        $this->client->getLogger()
                     ->debug('cURL response', array(
                         'code'    => $responseCode,
                         'headers' => $responseHeaders,
                         'body'    => $responseBody,
                     ));

        return array(
            $responseBody,
            $responseHeaders,
            $responseCode
        );
    }

    /**
     * Set options that update the transport implementation's behavior.
     *
     * @param $options
     */
    public function setOptions($options) {
        $this->options = $options + $this->options;
    }

    /**
     * Set the maximum request time in seconds.
     *
     * @param $timeout in seconds
     */
    public function setTimeout($timeout) {
        // Since this timeout is really for putting a bound on the time
        // we'll set them both to the same. If you need to specify a longer
        // CURLOPT_TIMEOUT, or a tigher CONNECTTIMEOUT, the best thing to
        // do is use the setOptions method for the values individually.
        $this->options[CURLOPT_CONNECTTIMEOUT] = $timeout;
        $this->options[CURLOPT_TIMEOUT]        = $timeout;
    }

    /**
     * Get the maximum request time in seconds.
     *
     * @return timeout in seconds
     */
    public function getTimeout() {
        return $this->options[CURLOPT_TIMEOUT];
    }

    /**
     * Test for the presence of a cURL header processing bug
     *
     * {@inheritDoc}
     *
     * @return boolean
     */
    protected function needsQuirk() {
        $ver        = curl_version();
        $versionNum = $ver['version_number'];

        return $versionNum < Google_IO_Curl::NO_QUIRK_VERSION;
    }
}
