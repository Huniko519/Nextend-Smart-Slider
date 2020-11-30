<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
if (is_numeric($settings->sliderid)) {
    echo '[smartslider3 slider=' . $settings->sliderid . ']';
} else {
    echo '[smartslider3 alias="' . $settings->sliderid . '"]';
}