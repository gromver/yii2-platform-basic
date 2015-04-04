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
    private $_activeMenuIds = [];
    private $_maps = [];

    public function init()
    {
        Yii::$app->urlManager->addRules([Yii::createObject([
            'class' => MenuUrlRule::className(),
            'menuManager' => $this
        ])], false); //вставляем в начало списка
    }

    /**
     * @param null $language
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

    public function setActiveMenu($value)
    {
        $this->_activeMenu = $value;
    }

    public function getActiveMenu()
    {
        return $this->_activeMenu;
    }

    public function addActiveMenuId($value)
    {
        $this->_activeMenuIds[] = $value;
    }

    public function setActiveMenuIds($value)
    {
        $this->_activeMenuIds = $value;
    }

    public function getActiveMenuIds()
    {
        return $this->_activeMenuIds;
    }

    public function applyMetaData()
    {
        if ($this->_activeMenu->metakey) {
            Yii::$app->getView()->registerMetaTag(['name' => 'keywords', 'content' => $this->_activeMenu->metakey], 'keywords');
        }
        if ($this->_activeMenu->metadesc) {
            Yii::$app->getView()->registerMetaTag(['name' => 'description', 'content' => $this->_activeMenu->metadesc], 'description');
        }
        if ($this->_activeMenu->robots) {
            Yii::$app->getView()->registerMetaTag(['name' => 'robots', 'content' => $this->_activeMenu->robots], 'robots');
        }
    }

    public function applyLayout()
    {
        if ($this->_activeMenu->layout_path) {
            // если в пункте меню установлен шаблон приложения, применяем его
            Yii::$app->controller->layout = $this->_activeMenu->layout_path;
        }
    }
}