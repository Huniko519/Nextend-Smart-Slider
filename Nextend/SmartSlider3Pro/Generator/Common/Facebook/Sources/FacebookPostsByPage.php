<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Facebook\Sources;

use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Radio;
use Nextend\Framework\Form\Element\Text;
use Nextend\Framework\Form\Element\Textarea;
use Nextend\Framework\Notification\Notification;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class FacebookPostsByPage extends AbstractGenerator {

    protected $layout = 'image';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_x('Facebook posts', 'Facebook generator type'));
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new Text($filter, 'page', n2_('Page'), 'Nextendweb');
        new Radio($filter, 'endpoint', n2_('Type'), 'posts', array(
            'options' => array(
                'posts' => n2_('Posts'),
                'feed'  => n2_('Feed')
            )
        ));
        new OnOff($filter, 'remove_spec_chars', n2_('Remove special characters'), 0);

        $configuration = $filterGroup->createRow('configuration');

        new Text($configuration, 'dateformat', n2_('Date format'), 'm-d-Y');
        new Text($configuration, 'timeformat', n2_('Time format'), 'H:i:s');
        new Textarea($configuration, 'sourcetranslatedate', n2_('Translate date and time'), 'January->January||February->February||March->March', array(
            'width'  => 300,
            'height' => 100
        ));
        new Text($configuration, 'excludetype', n2_('Exclude Types'), '', array(
            'tip' => n2_('Separate the types by a comma. E.g.: share,album')
        ));
    }

    protected function _getData($count, $startIndex) {

        $api = $this->group->getConfiguration()
                           ->getApi();

        $data = array();
        try {
            $request = $api->sendRequest('GET', $this->data->get('page', 'nextendweb') . '/' . $this->data->get('endpoint', 'feed'), array(
                'offset' => $startIndex,
                'limit'  => $count,
                'fields' => implode(',', array(
                    'from',
                    'updated_time',
                    'attachments',
                    'picture',
                    'message',
                    'story',
                    'full_picture'
                ))
            ));
            if (is_array($request) && isset($request['response_error'])) {
                Notification::error($request['response_error']);

                return null;
            } else {
                $result = $request->getDecodedBody();
            }

            $exclude_type = explode(',', $this->data->get('excludetype', ''));

            for ($i = 0; $i < count($result['data']); $i++) {
                if (!isset($result['data'][$i]['attachments']['data'][0]['type']) || !in_array($result['data'][$i]['attachments']['data'][0]['type'], $exclude_type)) {
                    $post = $result['data'][$i];

                    $attachments       = isset($post['attachments']) ? $post['attachments'] : '';
                    $remove_spec_chars = $this->data->get("remove_spec_chars", 0);

                    if (isset($attachments)) {

                        $record['link'] = isset($attachments['data'][0]['url']) ? $attachments['data'][0]['url'] : '';

                        $record['description'] = '';
                        $description_raw       = isset($attachments['data'][0]['description']) ? $attachments['data'][0]['description'] : '';
                        if ($remove_spec_chars) {
                            if (isset($description_raw) && !empty($description_raw)) {
                                $description           = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $this->makeClickableLinks($description_raw));
                                $record['description'] = str_replace("\n", "<br/>", $description);
                            } else {
                                $record['description'] = "";
                            }
                        } else {
                            $record['description'] = isset($description_raw) ? str_replace("\n", "<br/>", $this->makeClickableLinks($description_raw)) : '';
                        }

                        $record['type']   = isset($attachments['data'][0]['type']) ? $attachments['data'][0]['type'] : '';
                        $record['source'] = isset($attachments['data'][0]['media']['source']) ? $attachments['data'][0]['media']['source'] : '';

                    }

                    if ($remove_spec_chars) {
                        if (isset($post['message']) && !empty($post['message'])) {
                            $message           = iconv('UTF-8', 'ISO-8859-1//TRANSLIT//IGNORE', $this->makeClickableLinks($post['message']));
                            $record['message'] = str_replace("\n", "<br/>", $message);
                        } else {
                            $record['message'] = "";
                        }
                    } else {
                        $record['message'] = isset($post['message']) ? str_replace("\n", "<br/>", $this->makeClickableLinks($post['message'])) : '';
                    }
                    if (!isset($record['description'])) {
                        $record['description'] = $record['message'];
                    }

                    $record['story'] = isset($post['story']) ? $this->makeClickableLinks($post['story']) : '';
                    $record['image'] = isset($post['full_picture']) ? $post['full_picture'] : '';


                    $sourceTranslate = $this->data->get('sourcetranslatedate', '');
                    $translateValue  = explode('||', $sourceTranslate);
                    $translate       = array();
                    if ($sourceTranslate != 'January->January||February->February||March->March' && !empty($translateValue)) {
                        foreach ($translateValue as $tv) {
                            $translateArray = explode('->', $tv);
                            if (!empty($translateArray) && count($translateArray) == 2) {
                                $translate[$translateArray[0]] = $translateArray[1];
                            }
                        }
                    }
                    $record['date'] = $this->translate(date($this->data->get('dateformat', 'Y-m-d'), strtotime($result['data'][$i]['updated_time'])), $translate);
                    $record['time'] = date($this->data->get('timeformat', 'H:i:s'), strtotime($result['data'][$i]['updated_time']));

                    $data[] = &$record;
                    unset($record);
                }
            }
        } catch (\Exception $e) {
            Notification::error($e->getMessage());
        }

        return $data;
    }

    private function translate($from, $translate) {
        if (!empty($translate) && !empty($from)) {
            foreach ($translate as $key => $value) {
                $from = str_replace($key, $value, $from);
            }
        }

        return $from;
    }

}