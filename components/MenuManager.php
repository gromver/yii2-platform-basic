<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


use gromver\platform\basic\modules\menu\models\MenuItem;
use Yii;

/**
 * Class MenuManager
 * Компонент меню, доступен через Yii::$app->menuManager
 * Данный компонент инициирует маршрутизацию меню, через MenuUrlRule, а также дает доступ к активному пнукту меню
 * и картам пуктов меню [[MenuManager::getMenuMap]]
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property MenuItem $activeMenu
 * @property integer[] $activeMenuIds
 */
class MenuManager extends \yii\base\Object
{
    /**
     * @var MenuItem
     */
    private $_activeMenu;
    /**
     * @var integer[]
     */
    private $_activeMenuIds = [];
    /**
     * @var MenuMap[]
     */
    private $_maps = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        Yii::$app->urlManager->addRules([Yii::createObject([
            'class' => MenuUrlRule::className(),
            'menuManager' => $this
        ])], false); //вставляем в начало списка
    }

    /**
     * @param null|string $language
     * @return MenuMap
     */
    public function getMenuMap($language = null)
    {
        $language or $language = Yii::$app->language;
        if(!isset($this->_maps[$language]))
            $this->_maps[$language] = Yii::createObject([
                'class' => MenuMap::className(),
                'language' => $language,
            ]);

        return $this->_maps[$language];
    }

    /**
     * @param $value MenuItem
     */
    public function setActiveMenu($value)
    {
        $this->_activeMenu = $value;
    }

    /**
     * @return MenuItem
     */
    public function getActiveMenu()
    {
        return $this->_activeMenu;
    }

    /**
     * @param $value integer
     */
    public function addActiveMenuId($value)
    {
        $this->_activeMenuIds[] = $value;
    }

    /**
     * @param $value integer[]
     */
    public function setActiveMenuIds($value)
    {
        $this->_activeMenuIds = $value;
    }

    /**
     * @return integer[]
     */
    public function getActiveMenuIds()
    {
        return $this->_activeMenuIds;
    }

    /**
     * Применение метаданных акитвного пункта меню.
     */
    public function applyMetaData()
    {
        $view = Yii::$app->getView();
        if ($this->_activeMenu->metakey) {
            $view->registerMetaTag(['name' => 'keywords', 'content' => $this->_activeMenu->metakey], 'keywords');
        }
        if ($this->_activeMenu->metadesc) {
            $view->registerMetaTag(['name' => 'description', 'content' => $this->_activeMenu->metadesc], 'description');
        }
        if ($this->_activeMenu->robots) {
            $view->registerMetaTag(['name' => 'robots', 'content' => $this->_activeMenu->robots], 'robots');
        }
    }

    /**
     * Применение шаблона акитвного пункта меню.
     */
    public function applyLayout()
    {
        if ($this->_activeMenu->layout_path) {
            Yii::$app->controller->layout = $this->_activeMenu->layout_path;
        }
    }
}