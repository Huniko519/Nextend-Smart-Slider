<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\Common\Dribbble\Elements\DribbbleProjects;

class DribbbleProject extends AbstractGenerator {

    protected $layout = 'image_extended';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Dribble Projects');
    }

    public function renderFields($container) {

        Notification::notice(sprintf('%s is deprecated and will be removed after %s.', 'Dribbble generator', 'December 31st, 2020'));
        
        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));
        $filter      = $filterGroup->createRow('filter');
        new DribbbleProjects($filter, 'dribbble-project-id', 'Project', '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));
    }

    protected function _getData($count, $startIndex) {
        $data = array();
        $api  = $this->group->getConfiguration()
                            ->getApi();

        $result  = null;
        $success = $api->CallAPI('https://api.dribbble.com/v2/projects/' . $this->data->get('dribbble-project-id'), 'GET', array('per_page' => $count + $startIndex), array('FailOnAccessError' => true), $result);

        if (is_array($result)) {
            $shots = array_slice($result, $startIndex, $count);

            foreach ($shots AS $shot) {
                $user = $shot->user;
                $p    = array(
                    'image'        => isset($shot->images->hidpi) ? $shot->images->hidpi : $shot->images->normal,
                    'thumbnail'    => $shot->images->teaser,
                    'title'        => $shot->title,
                    'description'  => $shot->description,
                    'url'          => $shot->html_url,
                    'url_label'    => n2_('View'),
                    'image_normal' => $shot->images->normal,

                    'user_url'    => $user->html_url,
                    'user_avatar' => $user->avatar_url
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