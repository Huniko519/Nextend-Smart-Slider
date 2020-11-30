<?php


namespace Nextend\SmartSlider3\Application\Model;


use Exception;
use Nextend\Framework\Cache\AbstractCache;
use Nextend\Framework\Data\Data;
use Nextend\Framework\Database\Database;
use Nextend\Framework\Model\AbstractModelTable;
use Nextend\Framework\Notification\Notification;
use Nextend\Framework\Platform\Platform;
use Nextend\SmartSlider3\Application\Helper\HelperSliderChanged;
use Nextend\SmartSlider3\Slider\Admin\AdminSlider;
use Nextend\SmartSlider3\Slider\Slider;
use Nextend\SmartSlider3\SmartSlider3Info;

class ModelSliders extends AbstractModelTable {

    /**
     * @var ModelSlidersXRef
     */
    private $xref;

    protected function createConnectorTable() {

        $this->xref = new ModelSlidersXRef($this);

        return Database::getTable('nextend2_smartslider3_sliders');
    }

    public function get($id) {
        return Database::queryRow("SELECT * FROM " . $this->getTableName() . " WHERE id = :id", array(
            ":id" => $id
        ));
    }

    public function getByAlias($alias) {
        return Database::queryRow("SELECT id FROM " . $this->getTableName() . " WHERE alias = :alias", array(
            ":alias" => $alias
        ));
    }

    public function getWithThumbnail($id) {
        $slidesModel = new ModelSlides($this);

        return Database::queryRow("SELECT sliders.*, IF(sliders.thumbnail != '',sliders.thumbnail,(SELECT slides.thumbnail from " . $slidesModel->getTableName() . " AS slides WHERE slides.slider = sliders.id AND slides.published = 1 AND slides.generator_id = 0 AND slides.thumbnail NOT LIKE '' ORDER BY  slides.first DESC, slides.ordering ASC LIMIT 1)) AS thumbnail,
         IF(sliders.type != 'group', 
                        (SELECT count(*) FROM " . $slidesModel->getTableName() . " AS slides2 WHERE slides2.slider = sliders.id GROUP BY slides2.slider),
                        (SELECT count(*) FROM " . $this->xref->getTableName() . " AS xref2 WHERE xref2.group_id = sliders.id GROUP BY xref2.group_id)
                  ) AS slides
        FROM " . $this->getTableName() . " AS sliders
        WHERE sliders.id = :id", array(
            ":id" => $id
        ));
    }

    public function invalidateCache() {
        Database::query("DELETE FROM `" . Database::parsePrefix('#__nextend2_section_storage') . "` WHERE `application` LIKE 'cache'");

        Database::query("DELETE FROM `" . Database::parsePrefix('#__nextend2_section_storage') . "` WHERE `application` LIKE 'smartslider' AND `section` LIKE 'sliderChanged';");
    }

    public function refreshCache($sliderid) {
        AbstractCache::clearGroup(Slider::getCacheId($sliderid));
        AbstractCache::clearGroup(AdminSlider::getCacheId($sliderid));
        $this->markChanged($sliderid);
    }

    public function getSlidersCount() {
        $data = Database::queryRow("SELECT COUNT(*) AS sliders FROM " . $this->getTableName());
        if (!empty($data)) {
            return intval($data['sliders']);
        }

        return 0;
    }

    /**
     * @return mixed
     */
    public function getAll($groupID = '*', $status = '*', $orderBy = 'ordering', $orderByDirection = 'ASC') {
        $slidesModel = new ModelSlides($this);

        if (empty($orderBy)) {
            $orderBy = 'ordering';
        }
        if (empty($orderByDirection)) {
            $orderByDirection = 'ASC';
        }

        $_orderby = $orderBy . ' ' . $orderByDirection;

        $wheres = array();
        if ($groupID !== '*') {
            if ($groupID == 0) {
                $wheres[] = "(xref.group_id IS NULL OR xref.group_id = 0)";
            } else {
                if ($orderBy == 'ordering') {
                    $_orderby = 'xref.' . $orderBy . ' ' . $orderByDirection;
                }

                $wheres[] = "xref.group_id = '" . $groupID . "'";
            }
        }

        if ($status !== '*') {
            $wheres[] = "sliders.status LIKE " . Database::quote($status);
        }

        $sliders = Database::queryAll("
            SELECT sliders.*, 
                  IF(sliders.thumbnail != '',
                      sliders.thumbnail,
                          IF(sliders.type != 'group',
                              (SELECT slides.thumbnail FROM " . $slidesModel->getTableName() . " AS slides WHERE slides.slider = sliders.id AND slides.published = 1 AND slides.generator_id = 0 AND slides.thumbnail NOT LIKE '' ORDER BY  slides.first DESC, slides.ordering ASC LIMIT 1),
                              ''
                          )
                  ) AS thumbnail,
                  
                  IF(sliders.type != 'group', 
                        (SELECT count(*) FROM " . $slidesModel->getTableName() . " AS slides2 WHERE slides2.slider = sliders.id GROUP BY slides2.slider),
                        (SELECT count(*) FROM " . $this->xref->getTableName() . " AS xref2 LEFT JOIN " . $this->getTableName() . " AS sliders2 ON sliders2.id = xref2.slider_id WHERE xref2.group_id = sliders.id AND sliders2.status LIKE 'published' GROUP BY xref2.group_id)
                  ) AS slides
            FROM " . $this->getTableName() . " AS sliders
            LEFT JOIN " . $this->xref->getTableName() . " AS xref ON xref.slider_id = sliders.id
            WHERE " . implode(' AND ', $wheres) . "
            ORDER BY " . $_orderby);

        return $sliders;
    }

    public function _getAll() {
        return Database::queryAll("SELECT sliders.* FROM " . $this->getTableName() . " AS sliders");
    }

    public function getGroups($status = '*') {

        $wheres = array(
            "type LIKE 'group'"
        );

        if ($status !== '*') {
            $wheres[] = "status LIKE " . Database::quote($status);
        }

        return Database::queryAll("SELECT id, title FROM " . $this->getTableName() . " WHERE " . implode(' AND ', $wheres) . " ORDER BY title ASC");
    }

    public function import($slider, $groupID = 0) {
        try {
            $this->table->insert(array(
                'title'     => $slider['title'],
                'type'      => $slider['type'],
                'thumbnail' => empty($slider['thumbnail']) ? '' : $slider['thumbnail'],
                'params'    => $slider['params']->toJSON(),
                'time'      => date('Y-m-d H:i:s', Platform::getTimestamp())
            ));

            $sliderID = $this->table->insertId();

            if (isset($slider['alias'])) {
                $this->updateAlias($sliderID, $slider['alias']);
            }

            $this->xref->add($groupID, $sliderID);

            SmartSlider3Info::sliderChanged();

            return $sliderID;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function replace($slider, $groupID) {

        if (isset($slider['id']) && $slider['id'] > 0) {

            $groups = $this->xref->getGroups($slider['id']);

            $this->deletePermanently($slider['id']);

            try {
                $this->table->insert(array(
                    'id'        => $slider['id'],
                    'title'     => $slider['title'],
                    'type'      => $slider['type'],
                    'thumbnail' => empty($slider['thumbnail']) ? '' : $slider['thumbnail'],
                    'params'    => $slider['params']->toJSON(),
                    'time'      => date('Y-m-d H:i:s', Platform::getTimestamp())
                ));

                $sliderID = $this->table->insertId();

                if (isset($slider['alias'])) {
                    $this->updateAlias($sliderID, $slider['alias']);
                }

                if ($groupID) {
                    $this->xref->add($groupID, $sliderID);
                }

                if (!empty($groups)) {
                    foreach ($groups as $group) {
                        if ($groupID != $group['group_id']) {
                            $this->xref->add($group['group_id'], $sliderID);
                        }
                    }
                }

                SmartSlider3Info::sliderChanged();

                return $sliderID;
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }

        return $this->import($slider);
    }

    /**
     * @param $sliderId
     * @param $params Data
     */
    public function importUpdate($sliderId, $params) {

        $this->table->update(array(
            'params' => $params->toJson()
        ), array(
            "id" => $sliderId
        ));
    }

    public function create($slider, $groupID = 0) {
        if (!isset($slider['title'])) return false;
        if ($slider['title'] == '') $slider['title'] = n2_('New slider');

        $title = $slider['title'];
        unset($slider['title']);
        $type = $slider['type'];
        unset($slider['type']);

        $thumbnail = '';
        if (!empty($slider['thumbnail'])) {
            $thumbnail = $slider['thumbnail'];
            unset($slider['thumbnail']);
        }

        try {
            $this->table->insert(array(
                'title'     => $title,
                'type'      => $type,
                'params'    => json_encode($slider),
                'thumbnail' => $thumbnail,
                'time'      => date('Y-m-d H:i:s', Platform::getTimestamp()),
                'ordering'  => $this->getMaximalOrderValue()
            ));

            $sliderID = $this->table->insertId();

            $this->xref->add($groupID, $sliderID);

            SmartSlider3Info::sliderChanged();

            return $sliderID;
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function saveSimple($id, $title, $params) {
        if ($id <= 0) return false;

        if (empty($title)) $title = n2_('New slider');

        $this->table->update(array(
            'title'  => $title,
            'params' => json_encode($params)
        ), array(
            "id" => $id
        ));
    }

    public function save($id, $slider) {
        if (!isset($slider['title']) || $id <= 0) return false;
        $response = array(
            'changedFields' => array()
        );
        if ($slider['title'] == '') $slider['title'] = n2_('New slider');

        $title = $slider['title'];
        unset($slider['title']);
        $alias = $slider['alias'];
        unset($slider['alias']);
        $type = $slider['type'];
        unset($slider['type']);

        $thumbnail = '';
        if (!empty($slider['thumbnail'])) {
            $thumbnail = $slider['thumbnail'];
            unset($slider['thumbnail']);
        }

        $this->table->update(array(
            'title'     => $title,
            'type'      => $type,
            'params'    => json_encode($slider),
            'thumbnail' => $thumbnail
        ), array(
            "id" => $id
        ));

        $aliasResult = $this->updateAlias($id, $alias);
        if ($aliasResult !== false) {
            if ($aliasResult['oldAlias'] !== $aliasResult['newAlias']) {
                if ($aliasResult['newAlias'] === null) {
                    Notification::notice(n2_('Alias removed'));
                    $response['changedFields']['slideralias'] = '';
                } else if ($aliasResult['newAlias'] === '') {
                    Notification::error(n2_('Alias must contain one or more letters'));
                    $response['changedFields']['slideralias'] = '';
                } else {
                    Notification::notice(sprintf(n2_('Alias updated to: %s'), $aliasResult['newAlias']));
                    $response['changedFields']['slideralias'] = $aliasResult['newAlias'];
                }
            }
        }

        $this->markChanged($id);

        SmartSlider3Info::sliderChanged();

        return $response;
    }

    public function updateAlias($sliderID, $alias) {
        $isNull = false;
        if (empty($alias)) {
            $isNull = true;
        } else {

            $alias = strtolower($alias);
            $alias = preg_replace('/&.+?;/', '', $alias); // kill entities
            $alias = str_replace('.', '-', $alias);

            $alias = preg_replace('/[^%a-z0-9 _-]/', '', $alias);
            $alias = preg_replace('/\s+/', '-', $alias);
            $alias = preg_replace('|-+|', '-', $alias);
            $alias = preg_replace('|^-*|', '', $alias);

            if (empty($alias)) {
                $isNull = true;
            }
        }

        $slider = $this->get($sliderID);
        if ($isNull) {
            if ($slider['alias'] == 'null') {
            } else {
                Database::query('UPDATE ' . $this->table->getTableName() . ' SET `alias` = NULL WHERE id = ' . intval($sliderID));

                return array(
                    'oldAlias' => $slider['alias'],
                    'newAlias' => null
                );
            }
        } else {
            if (!is_numeric($alias)) {
                if ($slider['alias'] == $alias) {
                    return array(
                        'oldAlias' => $slider['alias'],
                        'newAlias' => $alias
                    );
                } else {
                    $_alias = $alias;
                    for ($i = 2; $i < 12; $i++) {
                        $sliderWithAlias = $this->getByAlias($_alias);
                        if (!$sliderWithAlias) {
                            $this->table->update(array(
                                'alias' => $_alias
                            ), array(
                                "id" => $sliderID
                            ));

                            return array(
                                'oldAlias' => $slider['alias'],
                                'newAlias' => $_alias
                            );
                            break;
                        } else {
                            $_alias = $alias . $i;
                        }
                    }
                }
            }

            return array(
                'oldAlias' => $slider['alias'],
                'newAlias' => ''
            );
        }

        return false;
    }

    public function setTitle($id, $title) {

        $this->table->update(array(
            'title' => $title
        ), array(
            "id" => $id
        ));

        $this->markChanged($id);

        return $id;
    }

    public function setThumbnail($id, $thumbnail) {

        $this->table->update(array(
            'thumbnail' => $thumbnail
        ), array(
            "id" => $id
        ));

        $this->markChanged($id);

        return $id;
    }

    public function changeSliderType($sliderID, $targetSliderType) {

        $this->table->update(array(
            'type' => $targetSliderType
        ), array(
            "id" => $sliderID
        ));

        $this->markChanged($sliderID);
    }

    /**
     * @param $sliderID
     * @param $groupID
     *
     * @return string
     */
    public function trash($sliderID, $groupID) {

        $relatedGroups = $this->xref->getGroups($sliderID);

        if (count($relatedGroups) > 1) {
            /**
             * Delete the connection between the slider and the group
             */
            $this->xref->deleteXref($groupID, $sliderID);

            return 'unlink';
        }

        $this->table->update(array(
            'status' => 'trash'
        ), array(
            "id" => $sliderID
        ));

        $helper = new HelperSliderChanged($this);
        $helper->setSliderChanged($sliderID, 1);
        $helper->setSliderChanged($groupID, 1);

        $slider = $this->get($sliderID);
        if ($slider['type'] == 'group') {
            $subSliders = $this->xref->getSliders($sliderID, 'published');
            foreach ($subSliders as $subSlider) {
                if (!$this->xref->isSliderAvailableInAnyGroups($subSlider['slider_id'])) {
                    $helper->setSliderChanged($subSlider['slider_id'], 1);
                }
            }
        }

        return 'trash';
    }

    public function restore($id) {
        $changedSliders = array();

        $slider = $this->get($id);
        if ($slider['type'] == 'group') {
            $subSliders = $this->xref->getSliders($id, 'published');
            foreach ($subSliders as $subSlider) {
                if (!$this->xref->isSliderAvailableInAnyGroups($subSlider['slider_id'])) {
                    $changedSliders[] = $subSlider['slider_id'];
                }
            }
        }

        $this->table->update(array(
            'status' => 'published'
        ), array(
            "id" => $id
        ));

        if (!empty($changedSliders)) {
            $helper = new HelperSliderChanged($this);
            foreach ($changedSliders as $sliderID) {
                $helper->setSliderChanged($sliderID, 1);
            }
        }
    }

    /**
     * @param $id
     *
     * @return array the IDs of the deleted sliders.
     */
    public function deletePermanently($id) {

        $slidesModel = new ModelSlides($this);
        $slidesModel->deleteBySlider($id);

        $deletedSliders = $this->xref->deleteGroup($id);

        $deletedSliders[] = $id;

        $this->xref->deleteSlider($id);
        $this->table->deleteByPk($id);

        AbstractCache::clearGroup(Slider::getCacheId($id));
        AbstractCache::clearGroup(AdminSlider::getCacheId($id));

        $this->markChanged($id);

        SmartSlider3Info::sliderChanged();

        return $deletedSliders;
    }

    public function trashOrDelete($id, $groupID) {

        $relatedGroups = $this->xref->getGroups($id);

        if (count($relatedGroups) > 1) {
            /**
             * Delete the connection between the slider and the group
             */
            $this->xref->deleteXref($groupID, $id);

            return 'unlink';
        } else {

            $this->deletePermanently($id);

            return 'delete';
        }
    }

    public function deleteSlides($id) {
        $slidesModel = new ModelSlides($this);
        $slidesModel->deleteBySlider($id);
        $this->markChanged($id);
    }

    public function duplicate($id, $withGroup = true) {

        $slider = $this->get($id);

        unset($slider['id']);

        $slider['title'] .= ' - ' . n2_('Copy');
        $slider['time']  = date('Y-m-d H:i:s', Platform::getTimestamp());

        /**
         * Remove alias to prevent override
         */
        $slider['alias'] = '';

        try {
            $this->table->insert($slider);
            $newSliderId = $this->table->insertId();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        if (!$newSliderId) {
            return false;
        }

        if ($slider['type'] == 'group') {
            $subSliders = $this->xref->getSliders($id, 'published');

            foreach ($subSliders as $subSlider) {
                $newSubSliderID = $this->duplicate($subSlider['slider_id'], false);
                $this->xref->add($newSliderId, $newSubSliderID);
            }

        } else {

            $slidesModel = new ModelSlides($this);

            foreach ($slidesModel->getAll($id) as $slide) {
                $slidesModel->copyTo($slide['id'], true, $newSliderId);
            }

            if ($withGroup) {
                $groups = $this->xref->getGroups($id);
                foreach ($groups as $group) {
                    $this->xref->add($group['group_id'], $newSliderId);
                }
            }
        }

        SmartSlider3Info::sliderChanged();

        return $newSliderId;
    }

    public function markChanged($sliderid) {

        $helper = new HelperSliderChanged($this);
        $helper->setSliderChanged($sliderid, 1);
    }

    public function order($groupID, $ids, $isReverse = false) {
        if (is_array($ids) && count($ids) > 0) {
            if ($isReverse) {
                $ids = array_reverse($ids);
            }
            $groupID = intval($groupID);
            if ($groupID <= 0) {
                $groupID = false;
            }
            $i = 0;
            foreach ($ids as $id) {
                $id = intval($id);
                if ($id > 0) {
                    if (!$groupID) {
                        $this->table->update(array(
                            'ordering' => $i,
                        ), array(
                            "id" => $id
                        ));
                    } else {
                        $this->xref->table->update(array(
                            'ordering' => $i,
                        ), array(
                            "slider_id" => $id,
                            "group_id"  => $groupID
                        ));
                    }

                    $i++;
                }
            }

            return $i;
        }

        return false;
    }

    protected function getMaximalOrderValue() {

        $query  = "SELECT MAX(ordering) AS ordering FROM " . $this->getTableName() . "";
        $result = Database::queryRow($query);

        if (isset($result['ordering'])) return $result['ordering'] + 1;

        return 0;
    }
}