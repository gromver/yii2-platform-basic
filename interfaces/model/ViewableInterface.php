<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\interfaces\model;

/**
 * Interface ViewableInterface
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface ViewableInterface {
    /**
     * Возвращает ссылку на просмотр модели во фронте
     * @return array | string route
     */
    public function getFrontendViewLink();

    /**
     * Тоже что и [[self::getFrontendViewLink]], только для моделей в виде массива
     * @param $model
     * @return array | string route
     */
    public static function frontendViewLink($model);

    /**
     * Возвращает ссылку на просмотр модели в бекенде
     * @return array | string route
     */
    public function getBackendViewLink();

    /**
     * Тоже что и [[self::getBackendViewLink]], только для моделей в виде массива
     * @param $model
     * @return array | string route
     */
    public static function backendViewLink($model);
}