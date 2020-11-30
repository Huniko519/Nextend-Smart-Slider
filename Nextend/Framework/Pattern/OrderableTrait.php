<?php

namespace Nextend\Framework\Pattern;

trait OrderableTrait {

    protected $ordering = 1000000;

    public function getOrdering() {
        return $this->ordering;
    }

    /**
     * @param OrderableTrait[] $items
     */
    public static function uasort(&$items) {
        uasort($items, array(
            OrderableTrait::class,
            'compare'
        ));
    }

    /**
     * @param OrderableTrait $a
     * @param OrderableTrait $b
     *
     * @return int
     */
    public static function compare($a, $b) {
        return $a->getOrdering() - $b->getOrdering();
    }
}