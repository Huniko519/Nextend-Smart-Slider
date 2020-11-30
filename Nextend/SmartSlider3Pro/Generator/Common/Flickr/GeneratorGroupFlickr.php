<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\Flickr;

use Nextend\SmartSlider3\Generator\AbstractGeneratorGroup;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Sources\FlickrPeopleAlbum;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Sources\FlickrPeoplePhotoGallery;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Sources\FlickrPeoplePhotoStream;
use Nextend\SmartSlider3Pro\Generator\Common\Flickr\Sources\FlickrPhotosSearch;

class GeneratorGroupFlickr extends AbstractGeneratorGroup {

    protected $name = 'flickr';

    protected $needConfiguration = true;

    public function __construct() {
        parent::__construct();

        $this->configuration = new ConfigurationFlickr($this);
    }

    public function getLabel() {
        return 'Flickr';
    }

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), 'Flickr');
    }

    protected function loadSources() {

        new FlickrPeoplePhotoStream($this, 'peoplephotostream', 'Photostream');
        new FlickrPeopleAlbum($this, 'peoplealbum', 'Album');
        new FlickrPeoplePhotoGallery($this, 'peoplephotogallery', 'Photogallery');
        new FlickrPhotosSearch($this, 'photossearch', n2_('Search'));
    }
}