<?php


namespace Nextend\SmartSlider3Pro\Renderable\Item\Area;


use Nextend\Framework\Parser\Color;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\View\Html;
use Nextend\SmartSlider3\Renderable\Item\AbstractItemFrontend;

class ItemAreaFrontend extends AbstractItemFrontend {

    public function render() {

        if ($this->hasLink()) {
            return $this->getLink($this->getHtml(false), array(
                'style' => 'display: block; width:100%;height:100%;',
                'class' => 'n2-ss-item-content n2-ow'
            ));
        }

        return $this->getHtml();
    }

    public function renderAdminTemplate() {
        return $this->getHtml();
    }

    private function getHtml($isContent = true) {
        $style = '';

        $color    = $this->data->get('color');
        $gradient = $this->data->get('gradient', 'off');

        if ($gradient != 'off') {
            $colorEnd = $this->data->get('color2');
            switch ($gradient) {
                case 'horizontal':
                    $style .= 'background:linear-gradient(to right, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'vertical':
                    $style .= 'background:linear-gradient(to bottom, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal1':
                    $style .= 'background:linear-gradient(45deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
                case 'diagonal2':
                    $style .= 'background:linear-gradient(135deg, ' . Color::colorToRGBA($color) . ' 0%,' . Color::colorToRGBA($colorEnd) . ' 100%);';
                    break;
            }
        } else {
            if (strlen($color) == 8 && substr($color, 6, 2) != '00') {
                $style = 'background-color: #' . substr($color, 0, 6) . ';';
                $style .= "background-color: " . Color::colorToRGBA($color) . ";";
            }
        }

        $_width = intval($this->data->get('width'));
        if ($_width > 0) {
            $style .= 'width:' . $_width . 'px;';
        }


        $height = '100%';
        
        $_height = intval($this->data->get('height'));
        if ($_height > 0) {
            $height = $_height . 'px';
        }

        $style .= 'height:' . $height . ';';


        $borderWidths = array();
        list($borderWidths[0], $borderWidths[1], $borderWidths[2], $borderWidths[3]) = (array)Common::parse($this->data->getIfEmpty('borderWidth', '0|*|0|*|0|*|0'));

        $hasBorder = false;
        for ($i = 0; $i < 4; $i++) {
            $borderWidths[$i] = max(0, intval($borderWidths[$i]));
            if ($borderWidths[$i] > 0) {
                $hasBorder = true;
            }
        }


        if ($hasBorder) {
            $borderRgba = Color::colorToRGBA($this->data->get('borderColor'));
            $style      .= 'border-width:' . implode('px ', explode('|*|', $this->data->get('borderWidth'))) . 'px;';
            $style      .= 'border-style: ' . $this->data->get('borderStyle') . ';';
            $style      .= 'border-color: ' . $borderRgba . ';';
            $style      .= 'box-sizing: border-box;';
        }
        $borderRadius = max(0, intval($this->data->get('borderRadius')));
        if ($borderRadius > 0) {
            $style .= 'border-radius:' . $borderRadius . 'px;';
        }

        return Html::tag('div', array(
            'class' => ($isContent ? 'n2-ss-item-content ' : '') . 'n2-ow',
            'style' => $style . $this->data->get('css')
        ));
    }

    public function needWidth() {
        return true;
    }

    public function needHeight() {
        return true;
    }
}