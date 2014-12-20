<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\version\models;

use gromver\platform\basic\behaviors\VersioningBehavior;
use gromver\platform\basic\modules\user\models\User;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "grom_history".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $item_id
 * @property string|\yii\db\ActiveRecord $item_class
 * @property string $version_note
 * @property string $version_hash
 * @property string $version_data
 * @property string $character_count
 * @property string $keep_forever
 * @property integer $created_at
 * @property integer $created_by
 */
class Version extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_version}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['version_data'], 'filter', 'filter' => function ($value) {
                    return is_string($value) ? $value : Json::encode($value);
                }],
            [['version_hash'], 'filter', 'filter' => function ($value) {
                    return $value === null ? self::hashData($this->version_data) : $value;
                }],
            [['created_at'], 'date', 'format' => 'dd.MM.yyyy HH:mm', 'timestampAttribute' => 'created_at', 'when' => function () {
                    return is_string($this->created_at);
                }],
            [['created_at'], 'integer', 'enableClientValidation' => false],
            [['item_id', 'item_class', 'version_data', 'version_hash'], 'required'],
            [['item_id', 'character_count', 'created_by'], 'integer'],
            [['version_data'], 'string'],
            [['item_class'], 'string', 'max' => 1024],
            [['version_note'], 'string', 'max' => 255],
            [['version_hash'], 'string', 'max' => 50],
            [['keep_forever'], 'string', 'max' => 2048]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'item_id' => Yii::t('gromver.platform', 'Item ID'),
            'item_class' => Yii::t('gromver.platform', 'Item Class'),
            'version_note' => Yii::t('gromver.platform', 'Version Note'),
            'version_hash' => Yii::t('gromver.platform', 'Version Hash'),
            'version_data' => Yii::t('gromver.platform', 'Version Data'),
            'character_count' => Yii::t('gromver.platform', 'Character Count'),
            'keep_forever' => Yii::t('gromver.platform', 'Keep Forever'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at']
                ]
            ],
            [
                'class' => BlameableBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_by']
                ]
            ],
            [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'character_count',
                    self::EVENT_BEFORE_UPDATE => 'character_count'
                ],
                'value' => function() {
                        return StringHelper::byteLength($this->version_data);
                    }
            ]
        ];
    }

    public function beforeSave($insert)
    {
        //можно только создавать версии
        /*if(!$insert)
            return false;*/

        //проверка на наличие идентичной записи
        if($insert && self::find()->andWhere(['item_id'=>$this->item_id, 'item_class'=>$this->item_class, 'version_hash'=>$this->version_hash])->exists())
            return false;

        return parent::beforeSave($insert);
    }

    public static function hashData($data)
    {
        return sha1(is_string($data) ? $data : Json::encode($data));
    }

    public function getVersionData()
    {
        return Json::decode($this->version_data);
    }

    public function getVersionHash()
    {
        return $this->version_hash;
    }

    public function getVersionNote()
    {
        return $this->version_note;
    }

    public function beforeDelete()
    {
        if($this->keep_forever)
            return false;
        else
            return parent::beforeDelete();
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return self::hasOne(User::className(), ['id'=>'created_by']);
    }

    /**
     * @return \yii\db\ActiveRecord
     */
    public function getDocument()
    {
        /** @var Version $version */
        $version_data = $this->getVersionData();

        $item_class = $this->item_class;
        /** @var \yii\db\ActiveRecord $model */
        $model = $item_class::findOne($this->item_id);

        //populate data
        foreach($version_data as $attribute=>$value)
            if($model->hasAttribute($attribute)) $model->{$attribute} = $value;

        return $model;
    }

    /**
     * @var array
     */
    private $_restoreErrors;

    public function getRestoreErrors()
    {
        return $this->_restoreErrors;
    }

    public function restore()
    {
        $model = $this->getDocument();
        $this->_restoreErrors = null;

        //отключаем поведения версионирования
        foreach ($model->getBehaviors() as $behavior) {
            if ($behavior instanceof VersioningBehavior) {
                $behavior->detach();
            }
        }

        if ($model->save()) {
            return true;
        }

        $this->_restoreErrors = $model->getFirstErrors();
        return false;
    }
}
