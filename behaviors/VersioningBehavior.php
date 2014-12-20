<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\behaviors;

use gromver\platform\basic\modules\version\models\Version;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

/**
 * Class VersioningBehavior
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property \yii\db\ActiveRecord $owner
 * @property array $currentVersionNote
 * @property array $versionData
 * @property string $versionHash
 */
class VersioningBehavior extends Behavior
{
    public $maxVersions = 3;
    public $attributes;
    public $versionNote;

    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }

    public function getVersion()
    {
        return $this->owner->hasOne(Version::className(), ['item_id'=>'id', 'item_class'=>'itemModelClass'])->andWhere(['version_hash'=>$this->getVersionHash()]);
    }

    public function getVersions()
    {
        return $this->owner->hasMany(Version::className(), ['item_id'=>'id', 'item_class'=>'itemModelClass'])->orderBy(['created_at'=>SORT_DESC]);
    }

    public function afterSave()
    {
        $version = new Version();
        $version->attributes = [
            'item_id' => $this->owner->id,
            'item_class' => $this->owner->className(),
            'version_data' => $this->getVersionData(),
            'version_note' => $this->versionNote
        ];

        $version->save();

        $expiredVersions = Version::find()->where(['item_id' => $version->item_id, 'item_class' => $version->item_class, 'keep_forever' => 0])->offset($this->maxVersions)->orderBy(['created_at'=>SORT_DESC])->all();
        foreach ($expiredVersions as $item) {
            $item->delete();
        }
    }

    /**
     * @return string
     */
    public function getCurrentVersionNote()
    {
        if (!isset($this->versionNote)) {
            $this->versionNote = $this->owner->version ? $this->owner->version->version_note : '';
        }

        return $this->versionNote;
    }

    /**
     * @return array
     */
    private function getVersionData()
    {
        if(isset($this->attributes) && is_array($this->attributes)) {
            $data = [];
            foreach($this->attributes as $attribute)
                $data[$attribute] = $this->owner->{$attribute};
        } else
            $data = $this->owner->toArray();

        return $data;
    }

    /**
     * @return string
     */
    private function getVersionHash()
    {
        return Version::hashData($this->getVersionData());
    }

    public function afterDelete()
    {
        foreach($this->owner->versions as $version) {
            $version->delete();
        }
    }

    /**
     * @return string
     */
    public function getItemModelClass()
    {
        return $this->owner->className();
    }

    /**
     * @param $version_id integer
     * @return bool
     */
    public function checkout($version_id)
    {
        /** @var Version $version */
        $version = Version::find(['id' => $version_id, 'item_id' => $this->owner->getPrimaryKey(), 'item_class' => $this->owner->className()]);
        if($version->restore()) {
            $this->owner->refresh();
            return true;
        }

        return false;
    }
}