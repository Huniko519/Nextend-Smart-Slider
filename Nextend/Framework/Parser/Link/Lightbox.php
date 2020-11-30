<?php
/**
 * @required N2SSPRO
 */

namespace Nextend\Framework\Parser\Link;

use Nextend\Framework\Asset\Predefined;
use Nextend\Framework\Platform\Platform;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;

class Lightbox implements ParserInterface {

    public function parse($argument, &$attributes) {
        if (!empty($argument)) {

            $attributes['data-n2-lightbox'] = '';

            if (!isset($attributes['class'])) $attributes['class'] = '';
            $attributes['class'] .= " n2-lightbox-trigger nolightbox no-lightbox";

            Predefined::loadLiteBox();

            $realUrls = array();
            $titles   = array();

            //JSON V2 storage
            if ($argument[0] == '{') {
                $data = json_decode($argument, true);
                for ($i = 0; $i < count($data['urls']); $i++) {
                    if (is_numeric($data['urls'][$i])) {
                        $data['urls'][$i] = Platform::getSiteUrl() . '?n2prerender=1&n2app=smartslider&n2controller=slider&n2action=iframe&sliderid=' . $data['urls'][$i] . '&hash=' . md5($data['urls'][$i] . NONCE_SALT);
                    }
                }
            

                $argument = ResourceTranslator::toUrl(array_shift($data['urls']));
                if (!empty($data['titles'][0])) {
                    $attributes['data-title'] = array_shift($data['titles']);
                }

                if (count($data['urls'])) {
                    if ($data['autoplay'] > 0) {
                        $attributes['data-autoplay'] = intval($data['autoplay']);
                    }

                    for ($i = 0; $i < count($data['urls']); $i++) {
                        if (!empty($data['urls'][$i])) {
                            $realUrls[] = ResourceTranslator::toUrl($data['urls'][$i]);
                            $titles[]   = !empty($data['titles'][$i]) ? $data['titles'][$i] : '';
                        }
                    }
                    $attributes['data-n2-lightbox-urls']   = implode(',', $realUrls);
                    $attributes['data-n2-lightbox-titles'] = implode('|||', $titles);
                    if (count($realUrls)) {
                        $attributes['data-litebox-group'] = md5(uniqid(mt_rand(), true));
                    }
                }

            } else {

                $urls     = explode(',', $argument);
                $parts    = explode(';', array_shift($urls), 2);
                $argument = ResourceTranslator::toUrl($parts[0]);
                if (!empty($parts[1])) {
                    $attributes['data-title'] = $parts[1];
                }

                if (count($urls)) {
                    if (intval($urls[count($urls) - 1]) > 0) {
                        $attributes['data-autoplay'] = intval(array_pop($urls));
                    }
                    for ($i = 0; $i < count($urls); $i++) {
                        if (!empty($urls[$i])) {
                            $parts      = explode(';', $urls[$i], 2);
                            $realUrls[] = ResourceTranslator::toUrl($parts[0]);
                            $titles[]   = !empty($parts[1]) ? $parts[1] : '';
                        }
                    }
                    $attributes['data-n2-lightbox-urls']   = implode(',', $realUrls);
                    $attributes['data-n2-lightbox-titles'] = implode('|||', $titles);
                    if (count($realUrls)) {
                        $attributes['data-litebox-group'] = md5(uniqid(mt_rand(), true));
                    }
                }
            }
        }

        return $argument;
    }
}