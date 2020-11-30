<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Parser\Common;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\Common\Vimeo\Elements\VimeoAlbums;
use Vimeo\Vimeo;

class VimeoAlbum extends AbstractGenerator {

    protected $layout = 'vimeo';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Vimeo');
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new VimeoAlbums($filter, 'album', 'Album', '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order-row');
        new GeneratorOrder($order, 'vimeoorder', '|*|asc', array(
            'options' => array(
                ''              => n2_('None'),
                'alphabetical'  => n2_('Alphabetic'),
                'comments'      => n2_('Comments'),
                'date'          => n2_('Date'),
                'default'       => n2_('Default'),
                'duration'      => n2_('Duration'),
                'likes'         => n2_('Likes'),
                'manual'        => n2_('Manual'),
                'modified_time' => n2_('Modified time'),
                'plays'         => n2_('Plays')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        $data = array();
        /** @var Vimeo $api */
        $api = $this->group->getConfiguration()
                           ->getApi();

        $album = $this->data->get('album', '');
        if (!empty($album)) {
            $args = array(
                'per_page' => $startIndex + $count
            );

            $order = Common::parse($this->data->get('vimeoorder', '|*|asc'));
            if (!empty($order[0])) {
                $args['sort'] = $order[0];
            }

            $response = $api->request($album . '/videos', $args);

            if ($response['status'] == 200) {
                $videos = array_slice($response['body']['data'], $startIndex, $count);

                foreach ($videos AS $video) {
                    $record = array();

                    $record['title']       = $video['name'];
                    $record['description'] = $video['description'];
                    $record['id']          = str_replace('/videos/', '', $video['uri']);
                    $record['url']         = 'https://vimeo.com/' . $record['id'];
                    $record['link']        = $video['link'];

                    foreach ($video['pictures']['sizes'] AS $picture) {
                        $record['image' . $picture['width'] . 'x' . $picture['height']]     = $picture['link'];
                        $record['imageplay' . $picture['width'] . 'x' . $picture['height']] = $picture['link_with_play_button'];
                    }

                    $data[] = &$record;
                    unset($record);
                }
            }
        }

        if ($order[1] == 'desc') {
            $data = array_reverse($data);
        }

        return $data;
    }
}