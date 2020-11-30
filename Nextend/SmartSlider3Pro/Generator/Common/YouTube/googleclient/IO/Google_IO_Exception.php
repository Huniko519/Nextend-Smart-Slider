<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\IO;


use Exception;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Google_Exception;
use Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Task\Google_Task_Retryable;

class Google_IO_Exception extends Google_Exception implements Google_Task_Retryable {

    /**
     * @var array $retryMap Map of errors with retry counts.
     */
    private $retryMap = array();

    /**
     * Creates a new IO exception with an optional retry map.
     *
     * @param string         $message
     * @param int            $code
     * @param Exception|null $previous
     * @param array|null     $retryMap Map of errors with retry counts.
     */
    public function __construct($message, $code = 0, Exception $previous = null, array $retryMap = null) {
        parent::__construct($message, $code, $previous);

        if (is_array($retryMap)) {
            $this->retryMap = $retryMap;
        }
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

        return 0;
    }
}
