<?php


namespace Nextend\SmartSlider3\SlideBuilder;


class BuilderComponentContent extends AbstractBuilderComponent {

    protected $defaultData = array(
        "type" => 'content',
        "name" => 'Content'
    );

    /** @var AbstractBuilderComponent[] */
    private $layers = array();

    /**
     *
     * @param AbstractBuilderComponent $container
     */
    public function __construct($container) {

        $container->add($this);
    }

    /**
     * @param $layer AbstractBuilderComponent
     */
    public function add($layer) {
        $this->layers[] = $layer;
    }

    public function getData() {
        $this->data['layers'] = array();
        foreach ($this->layers AS $layer) {
            $this->data['layers'][] = $layer->getData();
        }

        return parent::getData();
    }
}