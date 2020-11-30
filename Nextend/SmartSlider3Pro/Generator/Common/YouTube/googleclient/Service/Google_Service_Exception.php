<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Service;

use Exception;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Google_Exception;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Task\Google_Task_Retryable;

class Google_Service_Exception extends Google_Exception implements Google_Task_Retryable {

    /**
     * Optional list of errors returned in a JSON body of an HTTP error response.
     */
    protected $errors = array();

    /**
     * @var array $retryMap Map of errors with retry counts.
     */
    private $retryMap = array();

    /**
     * Override default constructor to add the ability to set $errors and a retry
     * map.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     * @param [{string, string}] errors List of errors returned in an HTTP
     *                                  response.  Defaults to [].
     * @param array|null     $retryMap  Map of errors with retry counts.
     */
    public function __construct($message, $code = 0, Exception $previous = null, $errors = array(), array $retryMap = null) {
        parent::__construct($message, $code, $previous);

        $this->errors = $errors;

        if (is_array($retryMap)) {
            $this->retryMap = $retryMap;
        }
    }

    /**
     * An example of the possible errors returned.
     *
     * {
     *   "domain": "global",
     *   "reason": "authError",
     *   "message": "Invalid Credentials",
     *   "locationType": "header",
     *   "location": "Authorization",
     * }
     *
     * @return [{string, string}] List of errors return in an HTTP response or [].
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Gets the number of times the associated task can be retried.
     *
     * NOTE: -1 is returned if the task can be retried indefinitely
     *
     * @return integer
     */
    public function allowedRetries() {
        if (isset($this->retryMap[$this->code])) {
            return $this->retryMap[$this->code];
        }

        $errors = $this->getErrors();

        if (!empty($errors) && isset($errors[0]['reason']) && isset($this->retryMap[$errors[0]['reason']])) {
            return $this->retryMap[$errors[0]['reason']];
        }

        return 0;
    }
}
