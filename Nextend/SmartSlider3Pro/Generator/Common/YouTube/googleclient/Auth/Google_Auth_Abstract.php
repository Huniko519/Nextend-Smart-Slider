<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Auth;

use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Http\Google_Http_Request;

/**
 * Abstract class for the Authentication in the API client
 *
 * @author Chris Chabot <chabotc@google.com>
 *
 */
abstract class Google_Auth_Abstract {

    /**
     * An utility function that first calls $this->auth->sign($request) and then
     * executes makeRequest() on that signed request. Used for when a request
     * should be authenticated
     *
     * @param Google_Http_Request $request
     *
     * @return Google_Http_Request $request
     */
    abstract public function authenticatedRequest(Google_Http_Request $request);

    abstract public function sign(Google_Http_Request $request);
}
