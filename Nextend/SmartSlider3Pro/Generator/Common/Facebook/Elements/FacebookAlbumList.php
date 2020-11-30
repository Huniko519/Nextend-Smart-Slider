<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Facebook\Elements;

use Facebook\Facebook;
use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\Select;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Request\Request;

class FacebookAlbumList extends Select {

    /** @var  Facebook|null */
    protected $api;

    protected function fetchElement() {
        $data = $this->getForm()
                     ->createAjaxUrl(array(
                         "generator/getData",
                         array(
                             'group' => Request::$REQUEST->getVar('group'),
                             'type'  => Request::$REQUEST->getVar('type')
                         )
                     ));

        Js::addInline('
            new N2Classes.FormElementFacebookAlbums("' . $this->fieldID . '", "' . $data . '");
        ');

        try {
            $id     = $this->getForm()
                           ->get('facebook-id', 'me');
            $albums = $this->api->get($id . '/albums');
            if (is_object($albums)) {
                $result = $albums->getDecodedBody();
                if (count($result['data'])) {
                    foreach ($result['data'] as $album) {
                        $this->options[$album['id']] = $album['name'];
                    }
                    if ($this->getValue() == '') {
                        $this->setValue($result['data'][0]['id']);
                    }
                }
            } else {
                Notification::error($albums['response_error']);
            }
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }

        return parent::fetchElement();
    }

    /**
     * @param Facebook|null $api
     */
    public function setApi($api) {
        $this->api = $api;
    }

}
