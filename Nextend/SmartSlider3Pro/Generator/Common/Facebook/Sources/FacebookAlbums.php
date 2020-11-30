<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Facebook\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\Common\Facebook\Elements\FacebookAlbumList;

class FacebookAlbums extends AbstractGenerator {

    protected $layout = 'image_extended';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Facebook Albums');
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new Text($filter, 'facebook-id', n2_('User or page'), 'me');
        new FacebookAlbumList($filter, 'facebook-album-id', n2_('Album'), '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));
    }

    protected function _getData($count, $startIndex) {

        $api = $this->group->getConfiguration()
                           ->getApi();

        $albumId = $this->data->get('facebook-album-id', '');

        $data = array();
        try {
            $request = $api->sendRequest('GET', $albumId . '/photos', array(
                'offset' => $startIndex,
                'limit'  => $count,
                'fields' => implode(',', array(
                    'from',
                    'images',
                    'name',
                    'link',
                    'likes',
                    'comments',
                    'icon',
                    'picture',
                    'source'
                ))
            ));
            if (is_object($request)) {
                $result = $request->getDecodedBody();

                for ($i = 0; $i < count($result['data']); $i++) {
                    $post = $result['data'][$i];

                    $record                = array();
                    $record['image']       = $post['images'][0]['source'];
                    $record['thumbnail']   = $post['images'][count($post['images']) - 1]['source'];
                    $record['title']       = $post['from']['name'];
                    $record['description'] = isset($post['name']) ? $this->makeClickableLinks($post['name']) : '';

                    $record['url']       = $record['link'] = $post['link'];
                    $record['url_label'] = 'View image';

                    $record['author_url'] = 'https://www.facebook.com/' . $post['from']['id'];

                    $record['likes']    = isset($post['likes']) && isset($post['likes']['data']) ? count($post['likes']['data']) : 0;
                    $record['comments'] = isset($post['comments']) && isset($post['comments']['data']) ? count($post['comments']['data']) : 0;

                    $record['icon']    = $post['icon'];
                    $record['picture'] = $post['picture'];
                    $record['source']  = $post['source'];

                    $x = 1;
                    foreach ($post['images'] AS $img) {
                        if ($x == 2 && $img["height"] < 960 && $img["width"] < 960) {
                            $record['image' . $x] = $img['source'];
                            $x++;
                        }
                        $record['image' . $x] = $img['source'];
                        $x++;
                    }

                    if ($x < 10) {
                        while ($x < 10) {
                            $record['image' . $x] = $img['source'];
                            $x++;
                        }
                    }

                    $data[$i] = &$record;
                    unset($record);
                }
            } else {
                Notification::error('Error with Facebook App: ' . $request['response_error']);
            }

        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }

        return $data;
    }
}