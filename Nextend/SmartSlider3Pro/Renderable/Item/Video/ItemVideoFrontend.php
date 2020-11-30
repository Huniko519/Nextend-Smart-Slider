<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Video;


use Nextend\Framework\Data\Data;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemVideoFrontend extends AbstractItemFrontend {

    public function render() {
        $owner = $this->layer->getOwner();

        $aspectRatio = $this->data->get('aspect-ratio', '16:9');
        if ($aspectRatio != 'fill') {
            $this->data->set('center', 0);
        }

        $owner->addScript('new N2Classes.FrontendItemVideo(this, "' . $this->id . '", ' . $this->data->toJSON() . ');');

        $style = '';
        if ($aspectRatio == 'custom') {
            $style = 'style="padding-top:' . ($this->data->get('aspect-ratio-height', '9') / $this->data->get('aspect-ratio-width', '16') * 100) . '%"';
        }

        return Html::tag("div", array(
            'class'             => 'n2_ss_video_player n2-ss-item-content n2-ss-item-video-container n2-ow-all',
            'data-aspect-ratio' => $aspectRatio
        ), '<div class="n2_ss_video_player__placeholder" ' . $style . '></div>' . Html::tag("video", $this->setOptions($this->data, $this->id), $this->setContent($owner, $this->data)));
    }

    public function renderAdminTemplate() {
        $aspectRatio = $this->data->get('aspect-ratio', '16:9');

        $style = '';
        if ($aspectRatio == 'custom') {
            $style = 'style="padding-top:' . ($this->data->get('aspect-ratio-height', '9') / $this->data->get('aspect-ratio-width', '16') * 100) . '%"';
        }

        return Html::tag('div', array(
            'class'             => 'n2_ss_video_player n2-ss-item-content n2-ss-item-video-container n2-ow-all',
            'data-aspect-ratio' => $aspectRatio,
            "style"             => 'background: URL(' . ResourceTranslator::toUrl('$ss3-frontend$/images/placeholder/video.png') . ') no-repeat 50% 50%; background-size: cover;'
        ), '<div class="n2_ss_video_player__placeholder" ' . $style . '></div>');
    }

    /**
     * @param $data Data
     * @param $id
     *
     * @return array
     */
    private function setOptions($data, $id) {
        $videoOptions = array(
            'style'        => '',
            'class'        => 'n2-ow intrinsic-ignore',
            'encode'       => false,
            'controlsList' => 'nodownload'
        );

        $videoOptions["data-volume"] = $data->get("volume", 1);
        if ($videoOptions["data-volume"] == 0) {
            $videoOptions['muted'] = 'muted';
        }

        if ($data->get('autoplay')) {
            $videoOptions['playsinline']        = 1;
            $videoOptions['webkit-playsinline'] = 1;
        }


        $videoOptions["id"] = $id;

        if ($data->get("showcontrols")) {
            $videoOptions["controls"] = "yes";
        } else {
            $videoOptions["style"] .= "pointer-events:none;";
        }

        $poster = $data->get("poster");
        if (!empty($poster)) {
            $videoOptions["poster"] = ResourceTranslator::toUrl($poster);
        }

        $fillMode              = $data->get("fill-mode", 'cover');
        $videoOptions["style"] .= "object-fit:" . $fillMode . ";";

        $videoOptions["preload"] = $data->get("preload", "auto");

        return $videoOptions;
    }

    /**
     * @param $owner AbstractRenderableOwner
     * @param $data  Data
     *
     * @return string
     */
    private function setContent($owner, $data) {
        $videoContent = "";

        if ($data->get("video_mp4", false)) {
            $videoContent .= Html::tag("source", array(
                "src"  => ResourceTranslator::toUrl($owner->fill($data->get("video_mp4"))),
                "type" => "video/mp4"
            ), '', false);
        }

        return $videoContent;
    }

    public function needWidth() {
        return true;
    }
}