<?php

namespace Nextend\SmartSlider3Pro\Form\Element;


use Nextend\Framework\Asset\Js\Js;
use Nextend\Framework\Form\Element\AbstractChooser;
use Nextend\SmartSlider3Pro\SplitText\SplitTextManager;

class SplitTextAnimation extends AbstractChooser {


    protected $relatedStyle = '';
    protected $relatedFont = '';
    protected $group = '';
    protected $transformOrigin = '';
    protected $preview = '';
    protected $linkedRelatedFields = array();

    protected function addScript() {

        Js::addInline('new N2Classes.FormElementSplitTextAnimationManager("' . $this->fieldID . '", {
            font: "' . $this->relatedFont . '",
            style: "' . $this->relatedStyle . '",
            preview: ' . json_encode($this->preview) . ',
            group: "' . $this->group . '",
            transformOrigin: "' . $this->transformOrigin . '",
            linkedRelatedFields: ' . json_encode($this->linkedRelatedFields) . ',
        });');
    }

    protected function fetchElement() {

        SplitTextManager::enqueue($this->getForm()
                                       ->getMVCHelper());

        return parent::fetchElement();
    }

    /**
     * @param string $relatedStyle
     */
    public function setRelatedStyle($relatedStyle) {
        $this->relatedStyle = $relatedStyle;
    }

    /**
     * @param string $relatedFont
     */
    public function setRelatedFont($relatedFont) {
        $this->relatedFont = $relatedFont;
    }

    /**
     * @param string $group
     */
    public function setGroup($group) {
        $this->group = $group;
    }

    /**
     * @param string $transformOrigin
     */
    public function setTransformOrigin($transformOrigin) {
        $this->transformOrigin = $transformOrigin;
    }

    /**
     * @param string $preview
     */
    public function setPreview($preview) {
        $this->preview = $preview;
    }

    /**
     * @param array $linkedRelatedFields
     */
    public function setLinkedRelatedFields($linkedRelatedFields) {
        $this->linkedRelatedFields = $linkedRelatedFields;
    }
}