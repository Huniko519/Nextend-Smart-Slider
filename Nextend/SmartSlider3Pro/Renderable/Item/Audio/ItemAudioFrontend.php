<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Audio;


use Nextend\Framework\Parser\Color;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\AbstractRenderableOwner;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemAudioFrontend extends AbstractItemFrontend {

    public function render() {
        $owner = $this->layer->getOwner();

        $owner->addScript('new N2Classes.FrontendItemAudio(this, "' . $this->id . '", ' . $this->data->toJSON() . ');');

        return $this->getHTML();
    }

    public function renderAdminTemplate() {
        return $this->getHTML();
    }

    public function getHTML() {
        $owner = $this->layer->getOwner();

        $this->loadResources($owner);

        $attributes = array(
            'class' => 'n2-ss-item-audio-bar n2-ow n2-ss-item-content n2-ow-all',
            'id'    => $this->id
        );

        $controls = array();

        if ($this->data->get('show')) {
            $attributes['data-state']  = 'paused';
            $attributes['data-volume'] = '1';
            $attributes['style']       = 'background-color:' . Color::colorToRGBA($this->data->get('color')) . ';';
            if (!$this->data->get('fullwidth')) {
                $attributes['style'] .= 'display:inline-flex;vertical-align:top;';
            }

            $controls[] = '<div class="n2-ss-item-audio-play"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M20 15.99c0 .41-.21.772-.52.967l-6.867 4.87c-.003 0-.006.002-.01.004l-.003.004c-.158.1-.342.156-.54.156-.585 0-1.06-.504-1.06-1.125v-9.752c0-.622.475-1.126 1.06-1.126.198 0 .382.058.54.157l.004.002.01.006 6.865 4.868c.31.196.52.556.52.97z"></path></svg></div>';
            $controls[] = '<div class="n2-ss-item-audio-pause"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M17 22V10h4v12h-4zm-6-12h4v12h-4V10z"></path></svg></div>';

            if ($this->data->get('show-progress')) {
                $controls[] = '<div class="n2-ss-item-audio-progress-container"><div class="n2-ss-item-audio-progress" style="background:{bar};"><div style="background:{color2};" class="n2-ss-item-audio-progress-playhead"></div></div></div>';
            }
            if ($this->data->get('show-time')) {
                $controls[] = '<div class="n2-ss-item-audio-time" style="color:{color2};">00:00 / 00:00</div>';
            }
            if ($this->data->get('show-volume')) {
                $controls[] = '<div class="n2-ss-item-audio-unmute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M15 22h-1l-4-4H9c-.45 0-1-.527-1-1v-3c0-.474.55-1 1-1h1l4-4h1v13z"/></svg></div>';
                $controls[] = '<div class="n2-ss-item-audio-mute"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="{color2}" d="M15 22h-1l-4-4H9c-.45 0-1-.527-1-1v-3c0-.474.55-1 1-1h1l4-4h1v13zm3.147-1.954l-.06.002c-.215 0-.423-.09-.577-.25l-.11-.116c-.286-.3-.32-.776-.078-1.117.612-.865.935-1.892.935-2.968 0-1.158-.367-2.244-1.06-3.14-.266-.342-.24-.837.055-1.148l.11-.115c.162-.172.38-.265.618-.25.23.012.446.126.592.313.963 1.236 1.472 2.737 1.472 4.34 0 1.49-.45 2.912-1.3 4.106-.143.2-.36.324-.597.342zm3.38 2.65c-.15.183-.363.293-.59.303H20.9c-.215 0-.423-.09-.577-.25l-.107-.114c-.3-.314-.32-.817-.048-1.158 1.32-1.644 2.045-3.733 2.045-5.88 0-2.236-.778-4.387-2.19-6.058-.285-.34-.27-.853.034-1.174l.107-.112c.16-.168.365-.26.603-.252.225.006.438.11.587.287C23.06 10.303 24 12.9 24 15.596c0 2.595-.878 5.116-2.474 7.1z"></path></svg></div>';
                $controls[] = '<div class="n2-ss-item-audio-volume-container"><div class="n2-ss-item-audio-volume" style="background:{bar};"><div style="background:{color2};" class="n2-ss-item-audio-volumehead"></div></div></div>';
            }
        } else if ($owner->isAdmin()) {
            $controls[]          = 'Audio';
            $attributes['style'] = 'color:#' . $this->data->get('color2') . ';';
        }

        return Html::tag('div', $attributes, $this->getAudioHTML($owner) . str_replace(array(
                '{bar}',
                '{color2}'
            ), array(
                Color::colorToRGBA($this->data->get('color2') . '33'),
                '#' . $this->data->get('color2')
            ), implode('', $controls)));
    }

    /**
     * @param AbstractRenderableOwner $slide
     *
     * @return string
     */
    private function getAudioHTML($slide) {
        $attributes = array();

        if ($this->data->get("volume", 1) == 0) {
            $attributes['muted'] = true;
        }

        return Html::tag("audio", $attributes, $this->setContent($slide));
    }


    /**
     * @param AbstractRenderableOwner $slide
     *
     * @return string
     */
    private function setContent($slide) {
        $videoContent = "";

        if ($this->data->get("audio_mp3", false)) {
            $videoContent .= Html::tag("source", array(
                "src"  => ResourceTranslator::toUrl($slide->fill($this->data->get("audio_mp3"))),
                "type" => "audio/mpeg"
            ), '', false);
        }

        return $videoContent;
    }

    /**
     * @param AbstractRenderableOwner $owner
     */
    public function loadResources($owner) {
        $owner->addLess(self::getAssetsPath() . "/audio.n2less", array(
            "sliderid" => $owner->getElementID()
        ));
    }
}