<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\interfaces\module;

/**
 * Interface MenuItemRoutesInterface
 * Возвращает список ссылок на компоненты модуля. Эти ссылки используется в пунктах меню для маршрутизации приложения
 * на конкретный компонент
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
interface MenuItemRoutesInterface
{
    /**
     * @return array массив описывающий компоненты на которые может ссылаться меню, структура:
     *
     * - label: string, описывает модуль предоставляющий список
     * - items: array, список роутов, структура
     *      - label: string, название компонента на который ведет роут
     *      - url: string, array, все точто поддерживается [[Url::to]], ссылка на вспомогательный компонент, возвращающий роут
     *      - route: string, непосредственно роут, если указан route то от url нет смысла
     * @see \gromver\platform\basic\widgets\MenuItemRoutes
     */
    public function getMenuItemRoutes();
}