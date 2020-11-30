<?php


namespace Nextend\Framework\View;


use Nextend\Framework\Pattern\GetPathTrait;
use Nextend\Framework\Pattern\MVCHelperTrait;

abstract class AbstractLayout {

    use GetPathTrait;
    use MVCHelperTrait;

    /** @var AbstractView */
    protected $view;

    /**
     * @var AbstractBlock[]|string[]|array[]
     */
    protected $contentBlocks = array();

    protected $state = array();

    /**
     * AbstractLayout constructor.
     *
     * @param AbstractView $view
     *
     */
    public function __construct($view) {
        $this->view = $view;

        $this->setMVCHelper($view);

        $this->getApplicationType()
             ->setLayout($this);

        $this->enqueueAssets();
    }

    protected function enqueueAssets() {

        $this->getApplicationType()
             ->enqueueAssets();
    }

    /**
     * @param string $html
     */
    public function addContent($html) {

        $this->contentBlocks[] = $html;
    }

    /**
     * @param AbstractBlock $block
     */
    public function addContentBlock($block) {

        $this->contentBlocks[] = $block;
    }

    public function displayContent() {
        foreach ($this->contentBlocks AS $content) {
            if (is_string($content)) {
                echo $content;
            } else if (is_array($content)) {
                echo call_user_func_array($content[0], $content[1]);
            } else {
                $content->display();
            }
        }
    }

    public function setState($name, $value) {
        $this->state[$name] = $value;
    }

    public abstract function render();
}