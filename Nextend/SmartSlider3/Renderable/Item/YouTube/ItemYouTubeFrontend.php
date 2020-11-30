<?php

namespace Nextend\SmartSlider3\Renderable\Item\YouTube;

use Nextend\Framework\Data\Data;
use Nextend\Framework\Image\Image;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;
use Nextend\SmartSlider3\Settings;

class ItemYouTubeFrontend extends AbstractItemFrontend {

    public function render() {
        $owner = $this->layer->getOwner();
        /**
         * @var Data
         */
        $this->data->fillDefault(array(
            'image'        => '',
            'aspect-ratio' => '16:9',
            'start'        => 0,
            'volume'       => -1,
            'autoplay'     => 0,
            'ended'        => '',
            'controls'     => 1,
            'center'       => 0,
            'loop'         => 0,
            'reset'        => 0,
            'related'      => 1,
        ));

        $aspectRatio = $this->data->get('aspect-ratio', '16:9');
        if ($aspectRatio != 'fill') {
            $this->data->set('center', 0);
        }

        $rawYTUrl = $owner->fill($this->data->get('youtubeurl', ''));

        $url_parts = parse_url($rawYTUrl);
        if (!empty($url_parts['query'])) {
            parse_str($url_parts['query'], $query);
            if (isset($query['v'])) {
                unset($query['v']);
            }
            $this->data->set("query", $query);
        }

        $youTubeUrl = $this->parseYoutubeUrl($rawYTUrl);

        $start = $owner->fill($this->data->get('start', ''));
        $this->data->set("youtubecode", $youTubeUrl);
        $this->data->set("start", $start);

        $end = $owner->fill($this->data->get('end', ''));
        $this->data->set("youtubecode", $youTubeUrl);
        $this->data->set("end", $end);

        $hasImage = 0;
        $image    = $owner->fill($this->data->get('image'));

        $coverImage = '';
        if (!empty($image)) {
            $style     = 'cursor:pointer; background: URL(' . ResourceTranslator::toUrl($image) . ') no-repeat 50% 50%; background-size: cover';
            $hasImage  = 1;
            $playImage = '';

            if ($this->data->get('playbutton', 1) == 1) {

                $playWidth  = intval($this->data->get('playbuttonwidth', '48'));
                $playHeight = intval($this->data->get('playbuttonheight', '48'));
                if ($playWidth > 0 && $playHeight > 0) {

                    $attributes = Html::addExcludeLazyLoadAttributes(array(
                        'style' => '',
                        'class' => ''
                    ));

                    $attributes['style'] .= 'width:' . $playWidth . 'px;';
                    $attributes['style'] .= 'height:' . $playHeight . 'px;';
                    $attributes['style'] .= 'margin-left:' . ($playWidth / -2) . 'px;';
                    $attributes['style'] .= 'margin-top:' . ($playHeight / -2) . 'px;';

                    $playButtonImage = $this->data->get('playbuttonimage', '');
                    if (!empty($playButtonImage)) {
                        $src = ResourceTranslator::toUrl($this->data->get('playbuttonimage', ''));
                    } else {
                        $src = Image::SVGToBase64('$ss3-frontend$/images/play.svg');
                    }

                    $playImage = Html::image($src, 'Play', $attributes);
                }
            }

            $coverImage = Html::tag('div', array(
                'class' => 'n2_ss_video_player__cover',
                'style' => $style
            ), $playImage);
        }

        $this->data->set('privacy-enhanced', intval(Settings::get('youtube-privacy-enhanced', 0)));

        $owner->addScript('new N2Classes.FrontendItemYouTube(this, "' . $this->id . '", ' . $this->data->toJSON() . ', ' . $hasImage . ');');

        $style = '';
        if ($aspectRatio == 'custom') {
            $style = 'style="padding-top:' . ($this->data->get('aspect-ratio-height', '9') / $this->data->get('aspect-ratio-width', '16') * 100) . '%"';
        }

        return Html::tag('div', array(
            'id'                => $this->id,
            'class'             => 'n2_ss_video_player n2-ss-item-content n2-ow-all',
            'data-aspect-ratio' => $aspectRatio
        ), '<div class="n2_ss_video_player__placeholder" ' . $style . '></div>' . Html::tag('div', array(
                'id' => $this->id . '-frame',
            ), '') . $coverImage);
    }

    public function renderAdminTemplate() {

        $aspectRatio = $this->data->get('aspect-ratio', '16:9');

        $style = '';
        if ($aspectRatio == 'custom') {
            $style = 'style="padding-top:' . ($this->data->get('aspect-ratio-height', '9') / $this->data->get('aspect-ratio-width', '16') * 100) . '%"';
        }

        $image = $this->layer->getOwner()
                             ->fill($this->data->get('image'));
        $this->data->set('image', $image);

        return Html::tag('div', array(
            'class'             => 'n2_ss_video_player n2-ow-all',
            'data-aspect-ratio' => $aspectRatio,
            "style"             => 'background: URL(' . ResourceTranslator::toUrl($this->data->getIfEmpty('image', '$ss3-frontend$/images/placeholder/video.png')) . ') no-repeat 50% 50%; background-size: cover;'
        ), '<div class="n2_ss_video_player__placeholder" ' . $style . '></div>' . ($this->data->get('playbutton', 1) ? '<div class="n2_ss_video_player__cover">' . Html::image(Image::SVGToBase64('$ss3-frontend$/images/play.svg'), 'Play', Html::addExcludeLazyLoadAttributes()) . '</div>' : ''));

    }

    private function parseYoutubeUrl($youTubeUrl) {
        preg_match('/^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#\&\?]*).*/', $youTubeUrl, $matches);

        if ($matches && isset($matches[7]) && strlen($matches[7]) == 11) {
            return $matches[7];
        }

        return $youTubeUrl;
    }

    public function needWidth() {
        return true;
    }
}