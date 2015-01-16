<?php

namespace gromver\platform\basic\modules\sqlsearch\models;

use gromver\platform\basic\interfaces\model\ViewableInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%grom_index}}".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $model_id
 * @property string $model_class
 * @property string $title
 * @property string $content
 * @property string $tags
 * @property integer $updated_at
 * @property string $url_frontend
 * @property string $url_backend
 */
class Index extends ActiveRecord implements ViewableInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_index}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['model_id', 'model_class', 'title', 'content', 'url_frontend', 'url_backend'], 'required'],
            [['model_id', 'updated_at'], 'integer'],
            [['content'], 'string'],
            [['content', 'title'], 'filter', 'filter' => function ($value) {
                return htmlspecialchars_decode(strip_tags($value));
            }],
            [['tags'], 'filter', 'filter' => function ($value) {
                return is_array($value) ? implode(' ', $value) : (string)$value;
            }],
            [['url_frontend', 'url_backend'], 'filter', 'filter' => function ($value) {
                return is_array($value) ? json_encode($value) : (string)$value;
            }],
            [['model_class'], 'string', 'max' => 255],
            [['title', 'tags', 'url_frontend', 'url_backend'], 'string', 'max' => 1024],
            [['model_id', 'model_class'], 'unique', 'targetAttribute' => ['model_id', 'model_class'], 'message' => 'The combination of Model ID and Model Class has already been taken.']
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
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                    self::EVENT_BEFORE_INSERT => 'updated_at'
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'model_id' => Yii::t('gromver.platform', 'Model ID'),
            'model_class' => Yii::t('gromver.platform', 'Model Class'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'content' => Yii::t('gromver.platform', 'Content'),
            'tags' => Yii::t('gromver.platform', 'Tags'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'url_frontend' => Yii::t('gromver.platform', 'Url Frontend'),
            'url_backend' => Yii::t('gromver.platform', 'Url Backend'),
        ];
    }

    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return json_decode($this->url_frontend, true);
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return json_decode($model['url_frontend'], true);
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return json_decode($this->url_backend, true);
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return json_decode($model['url_backend'], true);
    }
}
