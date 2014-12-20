<?php
/**
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\elasticsearch\models;


use gromver\platform\basic\interfaces\ViewableInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use Yii;
use yii\elasticsearch\ActiveRecord;

/**
 * Class ActiveRecord
 * Данный класс отслеживает состояние стандартных ActiveRecord объектов, в случае изменения, создания или удаления,
 * заносит соответсвующие изменения в ElasticSearch бд, тоесть служит своеобразным клеем для ActiveRecord и ElasticSearch
 * Связь с ActiveRecord определяется в статическом методе [[self::model()]]
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ActiveDocument extends ActiveRecord implements ViewableInterface {
    public function rules()
    {
        return [
            [$this->attributes(), 'safe']
        ];
    }

    public static $index = 'grom';

    public static function index()
    {
        return self::$index;
    }

    /**
     * @throws InvalidConfigException
     * @return string
     */
    public static function model()
    {
        throw new InvalidConfigException('The model() method of elasticsearch ActiveDocument has to be implemented by child classes.');
    }

    /**
     * @param \yii\db\ActiveRecord $model
     */
    public function loadModel($model)
    {
        $this->attributes = $model->toArray();
    }

    // ViewableInterface
    public function getFrontendViewLink()
    {
        /** @var ViewableInterface $modelClass */
        $modelClass = $this->model();

        return $modelClass::frontendViewLink($this);
    }

    public static function frontendViewLink($model)
    {
        /** @var ViewableInterface $modelClass */
        $modelClass = static::model();

        return $modelClass::frontendViewLink($model);
    }

    public function getBackendViewLink()
    {
        /** @var ViewableInterface $modelClass */
        $modelClass = $this->model();

        return $modelClass::backendViewLink($this);
    }

    public static function backendViewLink($model)
    {
        /** @var ViewableInterface $modelClass */
        $modelClass = static::model();

        return $modelClass::backendViewLink($model);
    }

    /**
     * Поисковый фильтр по умолчанию, применим для поиска во фронтенде, например фильтрация опубликованных постов
     * @return array
     * @see \gromver\platform\common\widgets\SearchResults
     */
    public static function searchDefaultFilter()
    {
        return [];
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

    private static $_documents = [];

    public static function watch($documentClasses)
    {
        foreach ($documentClasses as $class) {
            /** @var ActiveDocument|string $class */
            if (!is_subclass_of($class, __CLASS__)) {
                throw new InvalidConfigException("The {$class} class has to inherit from the class " . __CLASS__ . ".");
            }
            self::$_documents[$class::type()] = $class;
        }

        self::subscribe();
    }

    public static function registeredDocuments()
    {
        return self::$_documents;
    }

    public static function registeredTypes()
    {
        return array_keys(self::$_documents);
    }

    private static function subscribe()
    {
        static $subscribed;
        if (!isset($subscribed)) {
            Event::on(\yii\db\ActiveRecord::className(), \yii\db\ActiveRecord::EVENT_AFTER_INSERT, [self::className(), 'indexDocument']);
            Event::on(\yii\db\ActiveRecord::className(), \yii\db\ActiveRecord::EVENT_AFTER_UPDATE, [self::className(), 'indexDocument']);
            Event::on(\yii\db\ActiveRecord::className(), \yii\db\ActiveRecord::EVENT_AFTER_DELETE, [self::className(), 'deleteDocument']);
            $subscribed = true;
        }
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

    public static function findDocumentsByModelClass($modelClass)
    {
        return array_filter(self::$_documents, function($documentClass) use ($modelClass) {
            /** @var ActiveDocument $documentClass */
            return $documentClass::model() == $modelClass;
        });
    }

    public static function findDocumentByType($type)
    {
        return isset(self::$_documents[$type]) ? self::$_documents[$type] : false;
    }
}