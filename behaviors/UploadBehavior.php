<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\behaviors;

use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\validators\FileValidator;
use yii\validators\Validator;
use yii\web\UploadedFile;
use gromver\platform\basic\behaviors\upload\BaseProcessor;

/**
 * Class UploadBehavior
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class UploadBehavior extends Behavior {
    public $attributes;
    public $options = [];

    private $_ignoreUpdateEvents = false;
    private static $defaultOptions = [
        //'fileName' => '#name#.#extension#',
        'basePath' => '@webroot',
        'baseUrl' => '@web',
        'savePath' => 'upload'
    ];

    /**
     * Normalize attributes
     */
    public function normalizeAttributes()
    {
        foreach($this->attributes as $attribute=>$options) {
            if (is_int($attribute)) {
                $this->attributes[$options] = ArrayHelper::merge(self::$defaultOptions, $this->options);
            }
            else {
                $this->attributes[$attribute] = ArrayHelper::merge(self::$defaultOptions, $this->options, $options);
            }
        }
    }

    /**
     * @return array
     */
    public function attributes()
    {
        return array_keys($this->attributes);
    }

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if (!count($this->attributes))
            throw new InvalidConfigException(__CLASS__.'::attributes must be set.');

        $this->normalizeAttributes();
    }

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            ActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            ActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
            ActiveRecord::EVENT_AFTER_VALIDATE => 'afterValidate'
        ];
    }

    /**
     * @param $event \yii\base\ModelEvent
     */
    public function afterSave()
    {
        if ($this->_ignoreUpdateEvents) return;

        $this->populateUploadInstances();

        $update = [];

        foreach($this->attributes() as $attribute) {
            $file = $this->owner->$attribute;
            if (!$file instanceof UploadedFile) continue;

            if ($oldFileName = $this->owner->getOldAttribute($attribute)) {
                @unlink($this->getFilePath($attribute, $oldFileName));
            }

            $newFileName = $this->createFileName($attribute);
            $filePath = $this->getFilePath($attribute, $newFileName);
            $this->checkFilePath($attribute);
            if (!$file->saveAs($filePath, false)){
                Yii::warning('Saving '.$filePath.' is failed with error '.$file->error);
            }

            if ($processor = $this->getProcessor($attribute)) {
                $processor->process($filePath);
            }

            $update[$attribute] = $newFileName;
        }

        if (count($update)) {
            $this->owner->updateAttributes($update);
        }
    }

    /**
     * @param $event \yii\base\ModelEvent
     */
    public function afterDelete($event)
    {
        foreach($this->attributes() as $attribute) {
            @unlink($this->getFilePath($attribute));
        }
    }

    /**
     * @param $event \yii\base\Event
     */
    public function afterValidate($event)
    {
        $this->populateUploadInstances();

        foreach($this->attributes() as $attribute) {
            if ($validator = $this->getValidator($attribute)) {
                $validator->validateAttributes($this->owner);
            }
        }
    }
    /**
     * @param $attribute
     * @return array|null|object|Validator
     */
    private function getValidator($attribute)
    {
        if (!($validator = @$this->attributes[$attribute]['validate']))
            return null;

        if ($validator instanceof Validator) {
            return $validator;
        }

        if (is_array($validator)) {
            isset($validator['class']) or $validator['class'] = FileValidator::className();
            $validator['attributes'] = $attribute;
            return $this->attributes[$attribute]['validate'] = Yii::createObject($validator);
        }

        if (is_string($validator)) {
            return $this->attributes[$attribute]['validate'] = Validator::createValidator($validator, $this->owner, $attribute);
        }
    }

    /**
     * @param $attribute
     * @return array|null|\gromver\platform\basic\behaviors\upload\BaseProcessor
     * @throws \yii\base\InvalidConfigException
     */
    private function getProcessor($attribute)
    {
        if (!($process = @$this->attributes[$attribute]['process']))
            return null;

        if (is_array($process)) {
            return $this->attributes[$attribute]['process'] = Yii::createObject($process);
        }

        if (is_string($process)) {
            return $this->attributes[$attribute]['process'] = Yii::createObject(['class'=>$process]);
        }

        if ($process instanceof BaseProcessor) {
            return $process;
        }

        throw new InvalidConfigException('Обработчик файлов должен быть экземпляром класса ' . BaseProcessor::className());
    }

    /**
     * @param $attribute
     * @return mixed|string
     */
    protected function createFileName($attribute)
    {
        /** @var $file UploadedFile */
        if (($file = UploadedFile::getInstance($this->owner, $attribute)) instanceof UploadedFile) {
            //генерируем имя файла на основе шаблона
            $filename = @$this->attributes[$attribute]['fileName'];

            if ($filename instanceof \Closure)
                return $filename($file, $this->_model);

            if (is_string($filename) && !empty($filename)) {
                $fields = $this->owner->attributes();

                $search = array_map(function($value){
                    return '{' . $value . '}';
                }, $fields);

                $replace = array_map(function($attribute){
                    $value = $this->owner->$attribute;
                    return is_array($value) ? null: (string)$value;
                }, $fields);

                return str_replace(array_merge($search, ['#name#', '#extension#', '#attribute#']), array_merge($replace, [$file->getBaseName(), $file->getExtension(), $attribute]), $filename);
            }

            return $file->name;
        }
    }

    /**
     * загружет в соответсвующие поля хозяйской модели объекты UploadedFile
     */
    private function populateUploadInstances()
    {
        foreach($this->attributes() as $attribute) {
            if (!$this->owner->$attribute instanceof UploadedFile && ($instance = UploadedFile::getInstance($this->owner, $attribute)) && !$instance->getHasError()) {
                $this->owner->$attribute = $instance;
            }
        }
    }

    /**
     * @param $attribute string
     */
    private function checkFilePath($attribute)
    {
        FileHelper::createDirectory(Yii::getAlias($this->attributes[$attribute]['basePath'].DIRECTORY_SEPARATOR.$this->attributes[$attribute]['savePath'])/*, 0777*/);
    }

    /**
     * @param $attribute string
     * @return mixed|string
     */
    public function getFileName($attribute)
    {
        return $this->owner->$attribute;
    }

    /**
     * @param $attribute
     * @param null $fileName
     * @return bool|null|string
     */
    public function getFilePath($attribute, $fileName = null)
    {
        if (!($fileName or $fileName = $this->getFileName($attribute)))
            return false;

        return $fileName ? Yii::getAlias($this->attributes[$attribute]['basePath']).DIRECTORY_SEPARATOR.$this->attributes[$attribute]['savePath'].DIRECTORY_SEPARATOR.$fileName : null;
    }

    /**
     * @param $attribute
     * @param null $fileName
     * @return bool|null|string
     */
    public function getFileUrl($attribute, $fileName = null)
    {
        if (!($fileName or $fileName = $this->getFileName($attribute)))
            return false;

        return $fileName ? (Yii::getAlias($this->attributes[$attribute]['baseUrl'])?'/'.Yii::getAlias($this->attributes[$attribute]['baseUrl']):'').'/'.$this->attributes[$attribute]['savePath'].'/'.$fileName : null;
    }

    /**
     * @param $attribute string
     */
    public function deleteFile($attribute)
    {
        @unlink($this->getFilePath($attribute));
        $this->owner->$attribute = null;
        $this->_ignoreUpdateEvents = true;
        $this->owner->save(false);
        $this->_ignoreUpdateEvents = false;
    }
}