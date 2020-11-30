<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Sources;

use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class DribbbleShots extends AbstractGenerator {

    protected $layout = 'image_extended';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Dribble Shots');
    }

    public function renderFields($container) {
        Notification::notice(sprintf('%s is deprecated and will be removed after %s.', 'Dribble generator', 'December 31st, 2020'));
    }

    protected function _getData($count, $startIndex) {
        $data = array();
        $api  = $this->group->getConfiguration()
                            ->getApi();

        $result  = null;
        $success = $api->CallAPI('https://api.dribbble.com/v2/user/shots', 'GET', array('per_page' => $count + $startIndex), array('FailOnAccessError' => true), $result);
        if (is_array($result)) {
            $shots = array_slice($result, $startIndex, $count);

            foreach ($shots AS $shot) {
                $p = array(
                    'image'       => isset($shot->images->hidpi) ? $shot->images->hidpi : $shot->images->normal,
                    'thumbnail'   => $shot->images->teaser,
                    'title'       => $shot->title,
                    'description' => $shot->description,
                    'url'         => $shot->html_url,
                    'url_label'   => n2_('View'),

                    'image_normal' => $shot->images->normal
                );
                foreach ($shot->tags AS $j => $tag) {
                    $p['tag_' . ($j + 1)] = $tag;
                }
                $data[] = $p;
            }
        }

        return $data;
    }
}