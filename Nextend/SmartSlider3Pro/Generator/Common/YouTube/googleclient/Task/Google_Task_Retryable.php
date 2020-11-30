<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Task;


/**
 * Interface for checking how many times a given task can be retried following
 * a failure.
 */
interface Google_Task_Retryable {

    /**
     * Gets the number of times the associated task can be retried.
     *
     * NOTE: -1 is returned if the task can be retried indefinitely
     *
     * @return integer
     */
    public function allowedRetries();
}
