<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\behaviors;


use gromver\platform\basic\modules\tag\models\Tag;
use yii\db\ActiveQuery;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class TaggableBehavior
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \yii\db\ActiveRecord $owner
 */
class TaggableBehavior extends \yii\base\Behavior
{
    private $_tags;

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    /**
     * @param $value array массив с айдишниками тегов
     */
    public function setTags($value)
    {
        $this->_tags = empty($value) ? [] : $value;
    }

    /**
     * Tags relation
     * @return static
     */
    public function getTags()
    {
        return is_array($this->_tags) ? Tag::findAll(['id' => $this->_tags]) : $this->owner->hasMany(Tag::className(), ['id' => 'tag_id'])->viaTable(Tag::pivotTableName(), ['item_id' => 'id'], function($query) {
            /** @var $query ActiveQuery */
            $query->andWhere(['item_class' => $query->modelClass]);
        })->indexBy('id');
    }

    /**
     * @param $event \Yii\base\Event
     */
    public function afterSave($event)
    {
        $this->owner->getDb()->transaction(function() use ($event){
            if(isset($this->_tags) && is_array($this->_tags)) {
                $newTags = $this->_tags;
                $this->_tags = null;
                $oldTags = ArrayHelper::map($this->owner->tags, 'id', 'id');
                $this->owner->setIsNewRecord(false);

                $toAppend = array_diff($newTags, $oldTags);
                $toRemove = array_diff($oldTags, $newTags);

                foreach($toAppend as $id) {
                    if ($tag = Tag::findOne($id)) {
                        $this->owner->link('tags', $tag, ['item_class' => $this->owner->className()]);
                    }
                }

                if($event->name==BaseActiveRecord::EVENT_AFTER_UPDATE) {
                    foreach($toRemove as $id) {
                        if ($tag = Tag::findOne($id)) {
                            $this->owner->unlink('tags', $tag, true);
                        }
                    }
                }
            }
        });
    }

    public function afterDelete()
    {
        $this->owner->unlinkAll('tags', true);
    }
}