<?php


namespace Nextend\SmartSlider3\Application\Helper;


use Nextend\Framework\Data\Data;
use Nextend\Framework\Model\ApplicationSection;
use Nextend\Framework\Model\StorageSectionManager;
use Nextend\Framework\Pattern\MVCHelperTrait;
use Nextend\SmartSlider3\Application\Model\ModelSliders;
use Nextend\SmartSlider3\Application\Model\ModelSlidersXRef;
use WP_Post;

class HelperSliderChanged {

    use MVCHelperTrait;

    /** @var ApplicationSection */
    protected $storage;

    /**
     * HelperSliderChanged constructor.
     *
     * @param MVCHelperTrait $MVCHelper
     */
    public function __construct($MVCHelper) {

        $this->setMVCHelper($MVCHelper);

        $this->storage = StorageSectionManager::getStorage('smartslider');
    }


    public function isSliderChanged($sliderId, $value = 1) {
        return intval($this->storage->get('sliderChanged', $sliderId, $value));
    }

    public function setSliderChanged($sliderId, $value = 1) {
        $this->storage->set('sliderChanged', $sliderId, $value);
        $changedSliders = array($sliderId);

        $xref = new ModelSlidersXRef($this);
        foreach ($xref->getGroups($sliderId) AS $row) {
            if ($row['group_id'] > 0) {
                $this->storage->set('sliderChanged', $row['group_id'], $value);
                $changedSliders[] = $row['group_id'];
            }
        }
        $sliderModel  = new ModelSliders($this);
        $relatedPosts = array();
        foreach ($changedSliders AS $id) {

            do_action('smartslider3_slider_changed', $id);

            $slider = $sliderModel->get($id);
            if ($slider) {
                $sliderData      = new Data($slider['params'], true);
                $relatedPostsRaw = $sliderData->get('related-posts');
                if (!empty($relatedPostsRaw)) {
                    $relatedPostsRaw = explode("\n", str_replace(array(
                        "\r\n",
                        "\n\r",
                        "\r"
                    ), "\n", $relatedPostsRaw));
                    foreach ($relatedPostsRaw AS $relatedPostID) {
                        if ($relatedPostID > 0) {
                            $relatedPosts[] = $relatedPostID;
                        }
                    }
                }
            }
        }
        if (count($relatedPosts) > 0) {
            $relatedPosts = array_unique($relatedPosts);
            foreach ($relatedPosts AS $postID) {
                $post = WP_Post::get_instance($postID);
                if ($post) {
                    do_action('edit_post', $postID, $post);
                    do_action('save_post', $postID, $post);
                }
            }
        }
    
    }
}