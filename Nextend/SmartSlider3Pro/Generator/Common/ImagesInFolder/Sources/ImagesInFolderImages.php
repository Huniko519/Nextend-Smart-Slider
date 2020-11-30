<?php

namespace Nextend\SmartSlider3Pro\Generator\Common\ImagesInFolder\Sources;

use JURI;
use Nextend\Framework\Filesystem\Filesystem;
use Nextend\Framework\Form\Container\ContainerTable;
use Nextend\Framework\Form\Element\Mixed\GeneratorOrder;
use Nextend\Framework\Form\Element\OnOff;
use Nextend\Framework\Form\Element\Text\Folder;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Parser\Common;
use Nextend\Framework\ResourceTranslator\ResourceTranslator;
use Nextend\Framework\Url\Url;
use Nextend\SmartSlider3\Generator\AbstractGenerator;

class ImagesInFolderImages extends AbstractGenerator {

    protected $layout = 'image';

    public function getDescription() {
        return sprintf(n2_('Creates slides from %1$s.'), n2_('Images in folder'));
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

        new OnOff($filter, 'iptc', 'EXIF', 0);

        new OnOff($filter, 'remove_resize', 'Exclude resized images', 0, array(
            'tipLabel'       => n2_('Remove resized images'),
            'tipDescription' => n2_('This option removes files that match the "-[number]x[number].[extension]" pattern in the end of their file names. For example, "myimage.jpg" will stay in the generator result, but "myimage-120x120.jpg" will be removed, because it\'s the same image, just in a smaller size.'),
            'tipLink'        => 'https://smartslider.helpscoutdocs.com/article/1901-images-from-folder-generator#exclude-resized-images'
        ));


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
                Notification::error(n2_('Wrong path. This is the default image folder path, so try to navigate from here:') . '<br>*' . $root);

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
            $ext        = strtolower(pathinfo($files[$i], PATHINFO_EXTENSION));
            $extensions = array(
                'jpg',
                'jpeg',
                'png',
                'svg',
                'gif',
                'webp'
            );
            if (!in_array($ext, $extensions)) {
                array_splice($files, $i, 1);
            }
        }

        $IPTC = $this->data->get('iptc', 0) && function_exists('exif_read_data');

        $files = array_slice($files, $startIndex);

        $data = array();
        for ($i = 0; $i < $count && isset($files[$i]); $i++) {
            $image    = $this->pathToUri($folder . '/' . $files[$i], $media_folder);
            $data[$i] = array(
                'image'     => $image,
                'thumbnail' => $image,
                'title'     => $files[$i],
                'name'      => preg_replace('/\\.[^.\\s]{3,4}$/', '', $files[$i]),
                'created'   => filemtime($folder . '/' . $files[$i])
            );
            if ($IPTC) {
                $properties = @exif_read_data($folder . '/' . $files[$i]);
                if ($properties) {
                    foreach ($properties AS $key => $property) {
                        if (!is_array($property) && $property != '' && preg_match('/^[a-zA-Z]+$/', $key)) {
                            preg_match('/([2-9][0-9]*)\/([0-9]+)/', $property, $matches);
                            if (empty($matches)) {
                                $data[$i][$key] = $property;
                            } else {
                                $data[$i][$key] = round($matches[1] / $matches[2], 2);
                            }
                        }
                    }
                }
            }
        }

        if ($this->data->get('remove_resize', 0)) {
            $new = array();
            for ($i = 0; $i < count($data); $i++) {
                if (!preg_match('/[_-]\d+x\d+(?=\.[a-z]{3,4}$)/', $data[$i]['title'], $match)) {
                    $new[] = $data[$i];
                }
            }
            $data = $new;
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