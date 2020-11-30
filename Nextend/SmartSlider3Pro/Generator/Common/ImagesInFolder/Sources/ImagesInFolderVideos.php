<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder\Sources;

use JURI;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Form\Element\Text\Folder;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Url\Url;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class ImagesInFolderVideos extends AbstractGenerator {

    protected $layout = 'article';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('Videos in folder'));
    }

    private function trim($str, $path = true) {
        $str = ltrim(rtrim($str, '/'), '/');
        if ($path && strpos($str, ':') === false) {
            $str = '/' . $str;
        }

        return $str;
    }

    private function getSiteUrl() {
        $site_url = get_site_url();

        if (empty($site_url)) {
            $site_url = (empty($_SERVER['HTTPS']) ? "http://" : "https://") . $_SERVER['HTTP_HOST'];
        }

        return $this->trim($site_url, false);
    }

    private function getRootPath() {
        $root = '';
        $root = ABSPATH;

        if (!empty($root)) {
            $root = $this->trim($root);
        }

        return $root;
    }

    private function pathToUri($path, $media_folder = true) {
        $path = $this->trim($path);
        $root = $this->getRootPath();
        if (!empty($root) && !$media_folder) {
            $path = str_replace($root, '', $path);

            return $this->getSiteUrl() . $path;
        } else if ($media_folder) {
            return ResourceTranslator::urlToResource(Url::pathToUri($path));
        } else {
            return Url::pathToUri($path);
        }
    }

    public function renderFields($container) {

        $filterGroup = new ContainerTable($container, 'filter-group', n2_('Filter'));

        $filter = $filterGroup->createRow('filter');

        new Folder($filter, 'sourcefolder', n2_('Source folder'), '');

        $orderGroup = new ContainerTable($container, 'order-group', n2_('Order'));
        $order      = $orderGroup->createRow('order-row');
        new GeneratorOrder($order, 'order', '0|*|asc', array(
            'options' => array(
                '0' => n2_('None'),
                '1' => n2_('Filename'),
                '2' => n2_('Creation date')
            )
        ));
    }

    protected function _getData($count, $startIndex) {
        $root   = Filesystem::getImagesFolder();
        $source = $this->data->get('sourcefolder', '');
        if (substr($source, 0, 1) == '*') {
            $media_folder = false;
            $source       = substr($source, 1);
            if (!Filesystem::existsFolder($source)) {
                Notification::error(n2_('Wrong path. This is the default upload/media folder path, so try to navigate from here:') . '<br>*' . $root);

                return null;
            } else {
                $root = '';
            }
        } else {
            $media_folder = true;
        }
        $folder = Filesystem::realpath($root . '/' . ltrim(rtrim($source, '/'), '/'));
        $files  = Filesystem::files($folder);

        for ($i = count($files) - 1; $i >= 0; $i--) {
            $ext = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
            if ($ext != 'mp4') {
                array_splice($files, $i, 1);
            }
        }

        $files = array_slice($files, $startIndex);

        $data = array();
        for ($i = 0; $i < $count && isset($files[$i]); $i++) {
            $video    = $this->pathToUri($folder . '/' . $files[$i], $media_folder);
            $data[$i] = array(
                'video'   => $video,
                'title'   => $files[$i],
                'name'    => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]),
                'created' => filemtime($folder . '/' . $files[$i])
            );
        }

        list($orderBy, $sort) = Common::parse($this->data->get('order', '0|*|asc'));
        switch ($orderBy) {
            case 1:
                usort($data, array(
                    $this,
                    $sort
                ));
                break;
            case 2:
                usort($data, array(
                    $this,
                    'orderByDate_' . $sort
                ));
                break;
            default:
                break;
        }

        return $data;
    }

    public function asc($a, $b) {
        return (strtolower($b['title']) < strtolower($a['title']) ? 1 : -1);
    }

    public function desc($a, $b) {
        return (strtolower($a['title']) < strtolower($b['title']) ? 1 : -1);
    }

    public function orderByDate_asc($a, $b) {
        return ($b['created'] < $a['created'] ? 1 : -1);
    }

    public function orderByDate_desc($a, $b) {
        return ($a['created'] < $b['created'] ? 1 : -1);
    }
}