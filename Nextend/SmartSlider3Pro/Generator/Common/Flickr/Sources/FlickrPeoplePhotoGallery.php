<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Flickr\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Elements\FlickrGalleries;

class FlickrPeoplePhotoGallery extends AbstractGenerator {

    protected $layout = 'image_extended';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Flickr gallery');
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new FlickrGalleries($filter, 'peoplephotogallery', n2_('Album'), '', array(
            'api' => $this->group->getConfiguration()
                                 ->getApi()
        ));
    }

    protected function fallback($images) {
        foreach ($images AS $image) {
            if (!empty($image)) {
                return $image;
            }
        }

        return '';
    }

    protected function _getData($count, $startIndex) {

        $data = array();

        $galleryID = $this->data->get('peoplephotogallery', 0);
        if ($galleryID != 0) {

            $client = $this->group->getConfiguration()
                                  ->getApi();

            $result = $client->galleries_getPhotos($galleryID, 'description, date_upload, date_taken, owner_name, geo, tags, o_dims, views, media, path_alias, url_sq, url_t, url_s, url_q, url_m, url_n, url_z, url_c, url_l, url_o', $count + $startIndex);
            if (is_array($result['photos']['photo']) && !empty($result['photos']['photo'])) {
                $photos = array_slice($result['photos']['photo'], $startIndex, $count);
            } else {
                Notification::error(n2_('There are no photos in this gallery!'));

                return null;
            }

            $imageTypes = array(
                't',
                's',
                'q',
                'm',
                'n',
                'z',
                'c',
                'l',
                'o'
            );

            $ownerCache = array();
            $i          = 0;
            foreach ($photos AS $photo) {
                if (!isset($ownerCache[$photo['ownername']])) {
                    $owner                           = $client->people_findByUsername($photo['ownername']);
                    $ownerCache[$photo['ownername']] = $client->people_getInfo($owner['user']['nsid']);
                }
                $ow = $ownerCache[$photo['ownername']];

                foreach ($imageTypes AS $imageType) {
                    if (!isset($photo['url_' . $imageType])) {
                        $photo['url_' . $imageType] = '';
                    }
                }

                $data[$i]['image']       = $this->fallback(array(
                    $photo['url_o'],
                    $photo['url_l'],
                    $photo['url_z'],
                    $photo['url_m']
                ));
                $data[$i]['thumbnail']   = $this->fallback(array(
                    $photo['url_m'],
                    $photo['url_l'],
                    $photo['url_t']
                ));
                $data[$i]['title']       = $photo['title'];
                $data[$i]['description'] = $photo['description']['_content'];
                $data[$i]['url']         = $ow['person']['photosurl']['_content'];
                $data[$i]['url_label']   = n2_('View');

                $data[$i]['owner_username']       = $ow['person']['username']['_content'];
                $data[$i]['author_name']          = isset($ow['person']['realname']['_content']) ? $ow['person']['realname']['_content'] : $ow['person']['username']['_content'];
                $data[$i]['author_url']           = $ow['person']['profileurl']['_content'];
                $data[$i]['url_t']                = $photo['url_t'];
                $data[$i]['url_s']                = $photo['url_s'];
                $data[$i]['url_q']                = $photo['url_q'];
                $data[$i]['url_m']                = $photo['url_m'];
                $data[$i]['url_n']                = $photo['url_n'];
                $data[$i]['url_z']                = $photo['url_z'];
                $data[$i]['url_c']                = $photo['url_c'];
                $data[$i]['url_l']                = $photo['url_l'];
                $data[$i]['url_o']                = $photo['url_o'];
                $data[$i]['owner']                = $photo['owner'];
                $data[$i]['dateupload']           = $photo['dateupload'];
                $data[$i]['datetaken']            = $photo['datetaken'];
                $data[$i]['datetakengranularity'] = $photo['datetakengranularity'];
                $data[$i]['datetakenunknown']     = $photo['datetakenunknown'];
                $data[$i]['ownername']            = $photo['ownername'];
                $data[$i]['views']                = $photo['views'];
                $data[$i]['tags']                 = $photo['tags'];
                $data[$i]['latitude']             = $photo['latitude'];
                $data[$i]['longitude']            = $photo['longitude'];
                $data[$i]['accuracy']             = $photo['accuracy'];
                $data[$i]['context']              = $photo['context'];
                $data[$i]['media']                = $photo['media'];
                $data[$i]['media_status']         = $photo['media_status'];
                $data[$i]['url_sq']               = $photo['url_sq'];
                $i++;
            }
        } else {
            Notification::error(n2_('Please chooose a gallery!'));
        }

        return $data;
    }
}