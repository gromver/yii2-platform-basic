<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\elasticsearch\models;

use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\interfaces\ViewableInterface;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\elasticsearch\ActiveRecord;

/**
 * Class Index
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
 * @property mixed $params
 */
class Index extends ActiveRecord implements ViewableInterface {
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
            [['model_id', 'model_class'], 'unique', 'targetAttribute' => ['model_id', 'model_class'], 'message' => 'The combination of Model ID and Model Class has already been taken.'],
            [['params'], 'safe']
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
    public static function index()
    {
        return Yii::$app->getModule(ModuleQuery::instance()->implement('\gromver\platform\basic\modules\elasticsearch\Module')->find()[0])->elasticsearchIndex;
    }

    //баг ActiveDataProvider - почемуто пытается записать свойство _id
    public function get_Id()
    {
        return $this->getPrimaryKey(false);
    }

    public function set_Id($value)
    {
        $this->setPrimaryKey($value);
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ['id', 'model_class', 'model_id', 'title', 'content', 'tags', 'updated_at', 'url_frontend', 'url_backend', 'params'];
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
            'params' => Yii::t('gromver.platform', 'Params'),
        ];
    }

    /**
     * @param $event \yii\base\Event
     */
    public static function indexDocument($event)
    {
        /** @var \yii\db\ActiveRecord $model */
        if ($model = $event->sender) {
            foreach (self::findDocumentsByModelClass($model->className()) as $documentClass) {
                /** @var ActiveDocument $documentClass */
                if (!($document = $documentClass::get($model->getPrimaryKey()))) {
                    /** @var ActiveDocument $document */
                    $document = new $documentClass;
                    $document->setPrimaryKey($model->getPrimaryKey());
                }
                $document->loadModel($model);
                $document->save();
            }
        }
    }

    /**
     * @param $event \yii\base\Event
     */
    public static function deleteDocument($event)
    {
        /** @var \yii\db\ActiveRecord $model */
        if ($model = $event->sender) {
            foreach (self::findDocumentsByModelClass($model->className()) as $documentClass) {
                /** @var ActiveDocument $documentClass */
                if ($document = $documentClass::findOne($model->getPrimaryKey())) {
                    $document->delete();
                }
            }
        }
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