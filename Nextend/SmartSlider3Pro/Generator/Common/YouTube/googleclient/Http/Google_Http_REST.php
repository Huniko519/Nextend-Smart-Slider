<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Http;

use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Google_Client;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Service\Google_Service_Exception;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Task\Google_Task_Runner;

/**
 * This class implements the RESTful transport of apiServiceRequest()'s
 *
 * @author Chris Chabot <chabotc@google.com>
 * @author Chirag Shah <chirags@google.com>
 */
class Google_Http_REST {

    /**
     * Executes a Google_Http_Request and (if applicable) automatically retries
     * when errors occur.
     *
     * @param Google_Client       $client
     * @param Google_Http_Request $req
     *
     * @return array decoded result
     * @throws Google_Service_Exception on server side error (ie: not authenticated,
     *  invalid or malformed post body, invalid url)
     */
    public static function execute(Google_Client $client, Google_Http_Request $req) {
        $runner = new Google_Task_Runner($client, sprintf('%s %s', $req->getRequestMethod(), $req->getUrl()), array(
            get_class(),
            'doExecute'
        ), array(
            $client,
            $req
        ));

        return $runner->run();
    }

    /**
     * Executes a Google_Http_Request
     *
     * @param Google_Client       $client
     * @param Google_Http_Request $req
     *
     * @return array decoded result
     * @throws Google_Service_Exception on server side error (ie: not authenticated,
     *  invalid or malformed post body, invalid url)
     */
    public static function doExecute(Google_Client $client, Google_Http_Request $req) {
        $httpRequest = $client->getIo()
                              ->makeRequest($req);
        $httpRequest->setExpectedClass($req->getExpectedClass());

        return self::decodeHttpResponse($httpRequest, $client);
    }

    /**
     * Decode an HTTP Response.
     *
     * @static
     *
     * @param Google_Http_Request $response The http response to be decoded.
     * @param Google_Client       $client
     *
     * @return mixed|null
     * @throws Google_Service_Exception
     *
     */
    public static function decodeHttpResponse($response, Google_Client $client = null) {
        $code    = $response->getResponseHttpCode();
        $body    = $response->getResponseBody();
        $decoded = null;

        if ((intVal($code)) >= 300) {
            $decoded = json_decode($body, true);
            $err     = 'Error calling ' . $response->getRequestMethod() . ' ' . $response->getUrl();
            if (isset($decoded['error']) && isset($decoded['error']['message']) && isset($decoded['error']['code'])) {
                // if we're getting a json encoded error definition, use that instead of the raw response
                // body for improved readability
                $err .= ": ({$decoded['error']['code']}) {$decoded['error']['message']}";
            } else {
                $err .= ": ($code) $body";
            }

            $errors = null;
            // Specific check for APIs which don't return error details, such as Blogger.
            if (isset($decoded['error']) && isset($decoded['error']['errors'])) {
                $errors = $decoded['error']['errors'];
            }

            $map = null;
            if ($client) {
                $client->getLogger()
                       ->error($err, array(
                           'code'   => $code,
                           'errors' => $errors
                       ));

                $map = $client->getClassConfig('Google_Service_Exception', 'retry_map');
            }
            throw new Google_Service_Exception($err, $code, null, $errors, $map);
        }

        // Only attempt to decode the response, if the response code wasn't (204) 'no content'
        if ($code != '204') {
            $decoded = json_decode($body, true);
            if ($decoded === null || $decoded === "") {
                $error = "Invalid json in service response: $body";
                if ($client) {
                    $client->getLogger()
                           ->error($error);
                }
                throw new Google_Service_Exception($error);
            }

            if ($response->getExpectedClass()) {
                $class   = $response->getExpectedClass();
                $decoded = new $class($decoded);
            }
        }

        return $decoded;
    }

    /**
     * Parse/expand request parameters and create a fully qualified
     * request uri.
     *
     * @static
     *
     * @param string $servicePath
     * @param string $restPath
     * @param array  $params
     *
     * @return string $requestUrl
     */
    public static function createRequestUri($servicePath, $restPath, $params) {
        $requestUrl      = $servicePath . $restPath;
        $uriTemplateVars = array();
        $queryVars       = array();
        foreach ($params as $paramName => $paramSpec) {
            if ($paramSpec['type'] == 'boolean') {
                $paramSpec['value'] = ($paramSpec['value']) ? 'true' : 'false';
            }
            if ($paramSpec['location'] == 'path') {
                $uriTemplateVars[$paramName] = $paramSpec['value'];
            } else if ($paramSpec['location'] == 'query') {
                if (isset($paramSpec['repeated']) && is_array($paramSpec['value'])) {
                    foreach ($paramSpec['value'] as $value) {
                        $queryVars[] = $paramName . '=' . rawurlencode($value);
                    }
                } else {
                    $queryVars[] = $paramName . '=' . rawurlencode($paramSpec['value']);
                }
            }
        }

        if (count($uriTemplateVars)) {
            $uriTemplateParser = new Google_Utils_URITemplate();
            $requestUrl        = $uriTemplateParser->parse($requestUrl, $uriTemplateVars);
        }

        if (count($queryVars)) {
            $requestUrl .= '?' . implode('&', $queryVars);
        }

        return $requestUrl;
    }
}
