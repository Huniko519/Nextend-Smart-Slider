<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\YouTube\googleclient\Logger;


/**
 * Null logger based on the PSR-3 standard.
 *
 * This logger simply discards all messages.
 */
class Google_Logger_Null extends Google_Logger_Abstract {

    /**
     * {@inheritdoc}
     */
    public function shouldHandle($level) {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    protected function write($message, array $context = array()) {
    }
}
