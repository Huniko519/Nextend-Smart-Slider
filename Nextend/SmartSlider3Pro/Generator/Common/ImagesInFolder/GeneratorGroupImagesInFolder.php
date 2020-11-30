<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder\Sources\ImagesInFolderImages;
use Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder\Sources\ImagesInFolderSubfolders;
use Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder\Sources\ImagesInFolderVideos;

class GeneratorGroupImagesInFolder extends AbstractGeneratorGroup {

    protected $name = 'infolder';

    public function getLabel() {
        return n2_('Folder');
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('Images in folder'));
    }

    protected function loadSources() {

        new ImagesInFolderImages($this, 'images', n2_('Images in folder'));
        new ImagesInFolderSubfolders($this, 'subfolders', n2_('Images in folder and subfolders'));
        new ImagesInFolderVideos($this, 'videos', n2_('Videos in folder'));
    }
}