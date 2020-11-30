<?php

namespace Nextend\Framework\Asset\Js;

use Nextend\Framework\Asset\AbstractCache;
use Nextend\Framework\Cache\Manifest;

class Cache extends AbstractCache {

    public $outputFileType = "js";

    protected $initialContent = '(function(){var N=this;N.N2_=N.N2_||{r:[],d:[]},N.N2R=N.N2R||function(){N.N2_.r.push(arguments)},N.N2D=N.N2D||function(){N.N2_.d.push(arguments)}}).call(window);';

    /**
     * @param Manifest $cache
     *
     * @return string
     */
    public function getCachedContent($cache) {

        $content = '(function(){var N=this;N.N2_=N.N2_||{r:[],d:[]},N.N2R=N.N2R||function(){N.N2_.r.push(arguments)},N.N2D=N.N2D||function(){N.N2_.d.push(arguments)}}).call(window);';
        $content .= parent::getCachedContent($cache);
        $content .= "N2D('" . $this->group . "');";

        return $content;
    }
}