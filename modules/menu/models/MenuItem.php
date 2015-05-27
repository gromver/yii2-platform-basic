<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\menu\models;


use dosamigos\transliterator\TransliteratorHelper;
use gromver\platform\basic\behaviors\NestedSetsBehavior;
use gromver\platform\basic\components\UrlManager;
use gromver\platform\basic\interfaces\model\TranslatableInterface;
use gromver\platform\basic\interfaces\model\ViewableInterface;
use gromver\platform\basic\modules\widget\models\WidgetConfig;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;
use yii\helpers\Json;

/**
 * This is the model class for table "grom_menu_item".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $menu_type_id
 * @property integer $parent_id
 * @property integer $translation_id
 * @property integer $status
 * @property string $language
 * @property string $title
 * @property string $alias
 * @property string $path
 * @property string $note
 * @property string $link
 * @property integer $link_weight
 * @property integer $link_type
 * @property string $link_params
 * @property string $layout_path
 * @property string $access_rule
 * @property string $metakey
 * @property string $metadesc
 * @property string $robots
 * @property integer $secure
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property string $ordering
 * @property string $hits
 * @property string $lock
 *
 * @property string $linkTitle
 * @property array $linkParams
 * @property integer $context
 * @property MenuType $menuType
 * @property MenuItem $parent
 * @property MenuItem[] $translations
 * @property array $layoutLabels
 */
class MenuItem extends \yii\db\ActiveRecord implements ViewableInterface, TranslatableInterface
{
    const STATUS_UNPUBLISHED = 0;
    const STATUS_PUBLISHED = 1;
    const STATUS_MAIN_PAGE = 2;

    const LINK_ROUTE = 1;   //MenuItem::link используется в качестве роута, MenuItem::path в качестве ссылки
    const LINK_HREF = 2;    //MenuItem::link используется в качестве ссылки, MenuItem::path не используется

    const CONTEXT_PROPER = 1;
    const CONTEXT_APPLICABLE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_menu_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['menu_type_id', 'status', 'link_type', 'link_weight', 'secure', 'created_at', 'updated_at', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock'], 'integer'],
            [['menu_type_id'], 'required'],
            [['menu_type_id'], 'exist', 'targetAttribute' => 'id', 'targetClass' => MenuType::className()],
            [['language'], 'required'],
            [['language'], 'string', 'max' => 7],
            [['language'], function($attribute) {
                if (($parent = self::findOne($this->parent_id)) && !$parent->isRoot() && $parent->language != $this->language) {
                    $this->addError($attribute, Yii::t('gromver.platform', 'Language has to match with the parental.'));
                }
            }],
            [['layout_path'], 'filter', 'filter' => function($value) {
                // если во вьюхе используется select2, отфильтровываем значение из массива [0 => 'значение'] -> 'значение'
                return is_array($value) ? reset($value) : $value;
            }],
            [['title', 'link', 'layout_path'], 'string', 'max' => 1024],
            [['alias', 'note', 'metakey'], 'string', 'max' => 255],
            [['metadesc'], 'string', 'max' => 2048],
            [['access_rule', 'robots'], 'string', 'max' => 50],

            [['parent_id'], 'filter', 'filter' => function ($value) {
                return $value ? $value : self::find()->select('id')->roots()->scalar();
            }],
            [['parent_id'], 'integer'],
            [['parent_id'], 'filter', 'filter' => 'intval'],
            [['parent_id'], 'exist', 'targetAttribute' => 'id'],
            [['parent_id'], 'compare', 'compareAttribute' => 'id', 'operator' => '!='],
            [['parent_id'], function($attribute) {
                if (($parent = self::findOne($this->parent_id)) && !$parent->isRoot() && $parent->menu_type_id != $this->menu_type_id) {
                    $this->addError($attribute, Yii::t('gromver.platform', 'Parental point of the menu doesn\'t correspond to the chosen menu type.'));
                }
            }],
            [['status'], function($attribute) {
                if ($this->status == self::STATUS_MAIN_PAGE && $this->link_type == self::LINK_HREF) {
                    $this->addError($attribute, Yii::t('gromver.platform', 'Alias of the menu item can\'t be a main page.'));
                }
            }],
            [['alias'], 'filter', 'filter' => 'trim'],
            [['alias'], 'filter', 'filter' => function($value){
                    if (empty($value)) {
                        return Inflector::slug(TransliteratorHelper::process($this->title));
                    } else {
                        return Inflector::slug($value);
                    }
                }],
            [['alias'], 'unique', 'filter' => function($query) {
                    /** @var $query \yii\db\ActiveQuery */
                    if ($parent = self::findOne($this->parent_id)){
                        $query->andWhere('lft>=:lft AND rgt<=:rgt AND level=:level AND language=:language', [
                                'lft' => $parent->lft,
                                'rgt' => $parent->rgt,
                                'level' => $parent->level + 1,
                                'language' => $this->language
                            ]);
                    }
                }],
            [['alias'], 'string', 'max' => 255],
            [['alias'], 'required', 'enableClientValidation' => false],
            [['translation_id'], 'unique', 'filter' => function($query) {
                /** @var $query \yii\db\ActiveQuery */
                $query->andWhere(['language' => $this->language]);
            }, 'message' => Yii::t('gromver.platform', 'Localization ({language}) for item (ID: {id}) already exists.', ['language' => $this->language, 'id' => $this->translation_id])],
            [['title',  'link', 'status'], 'required'],
            [['ordering'], 'filter', 'filter' => 'intVal'], //for proper $changedAttributes
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'menu_type_id' => Yii::t('gromver.platform', 'Menu Type ID'),
            'parent_id' => Yii::t('gromver.platform', 'Parent ID'),
            'translation_id' => Yii::t('gromver.platform', 'Translation ID'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'language' => Yii::t('gromver.platform', 'Language'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'alias' => Yii::t('gromver.platform', 'Alias'),
            'path' => Yii::t('gromver.platform', 'Path'),
            'note' => Yii::t('gromver.platform', 'Note'),
            'link' => Yii::t('gromver.platform', 'Link'),
            'link_type' => Yii::t('gromver.platform', 'Link Type'),
            'link_weight' => Yii::t('gromver.platform', 'Link Weight'),
            'link_params' => Yii::t('gromver.platform', 'Link Params'),
            'layout_path' => Yii::t('gromver.platform', 'Layout Path'),
            'access_rule' => Yii::t('gromver.platform', 'Access Rule'),
            'metakey' => Yii::t('gromver.platform', 'Meta keywords'),
            'metadesc' => Yii::t('gromver.platform', 'Meta description'),
            'robots' => Yii::t('gromver.platform', 'Robots'),
            'secure' => Yii::t('gromver.platform', 'Secure'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
            'updated_by' => Yii::t('gromver.platform', 'Updated By'),
            'lft' => Yii::t('gromver.platform', 'Lft'),
            'rgt' => Yii::t('gromver.platform', 'Rgt'),
            'level' => Yii::t('gromver.platform', 'Level'),
            'ordering' => Yii::t('gromver.platform', 'Ordering'),
            'hits' => Yii::t('gromver.platform', 'Hits'),
            'lock' => Yii::t('gromver.platform', 'Lock'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            NestedSetsBehavior::className()
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMenuType()
    {
        return $this->hasOne(MenuType::className(), ['id' => 'menu_type_id']);
    }

    /**
     * @return MenuItemQuery
     */
    public function getParent() {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    /**
     * @inheritdoc
     * @return MenuItemQuery
     */
    public static function find()
    {
        return new MenuItemQuery(get_called_class());
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public function optimisticLock()
    {
        return 'lock';
    }

    public function saveNode($runValidation = true, $attributes = null) {
        if ($this->getIsNewRecord()) {
            if($parent = self::findOne($this->parent_id)) {
                return $this->appendTo($parent, $runValidation, $attributes);
            } else {
                return $this->makeRoot($parent, $runValidation, $attributes);
            }
        }
        // меню перемещено в другой пункт меню
        if ($this->getOldAttribute('parent_id') != $this->parent_id && $parent = self::findOne($this->parent_id)) {
            return $this->appendTo($parent, $runValidation, $attributes);
        }
        // просто апдейт
        return $this->save($runValidation, $attributes);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // устанавливаем translation_id по умолчанию
        if ($insert && $this->translation_id === null) {
            $this->updateAttributes([
                'translation_id' => $this->id
            ]);
        }

        //Если изменен тип меню или язык, смена языка возможна только для корневых пунктов меню
        if (array_key_exists('menu_type_id', $changedAttributes) || array_key_exists('language', $changedAttributes)) {
            $this->normalizeLanguage();
        }

        if (array_key_exists('status', $changedAttributes)) {
            $this->normalizeStatus();
        }

        $oldPath = $this->getOldAttribute('path');
        // нормализуем контексты виджетов и пути подкатегорий для текущей категории при её перемещении либо изменении псевдонима
        if (array_key_exists('parent_id', $changedAttributes) || array_key_exists('alias', $changedAttributes)) {
            $this->refresh();
            $this->normalizePath();
            $this->normalizeWidgets($oldPath);
        }

        // ранжируем категории если нужно
        if (array_key_exists('ordering', $changedAttributes)) {
            $this->ordering ? $this->parent->reorderNode('ordering') : $this->parent->reorderNode('lft');
        }
    }

    /**
     * @return string
     */
    private function calculatePath()
    {
        $aliases = $this->parents()->excludeRoots()->select('alias')->column();
        return empty($aliases) ? $this->alias : implode('/', $aliases) . '/' . $this->alias;
    }

    public function normalizePath($parentPath = null)
    {
        if ($parentPath === null) {
            $path = $this->calculatePath();
        } else {
            $path = $parentPath . '/' . $this->alias;
        }

        $this->updateAttributes(['path' => $path]);

        $children = $this->children(1)->all();
        foreach ($children as $child) {
            /** @var self $child */
            $child->normalizePath($path);
        }
    }

    public function normalizeLanguage()
    {
        $ids = $this->children()->select('id')->column();
        self::updateAll(['menu_type_id' => $this->menu_type_id, 'language' => $this->language], ['id' => $ids]);
    }

    //для каждого языка возможен только один пукнт меню со статусом главной страницы
    public function normalizeStatus()
    {
        if ($this->status == self::STATUS_MAIN_PAGE) {
            self::updateAll(['status' => self::STATUS_PUBLISHED], 'status=:status AND language=:language AND id!=:id', [':status' => self::STATUS_MAIN_PAGE, ':id' => $this->id, ':language' => $this->language]);
        }
    }

    public function normalizeWidgets($oldPath)
    {
        if ($oldPath) {
            foreach(WidgetConfig::find()->where(['like', 'context', $oldPath.'%', false])->each() as $config) {
                $config->context = preg_replace("#^{$oldPath}#", $this->path, $config->context);
                $config->save();
            }
        }
    }

    /**
     * @return array
     */
    public function getLinkParams()
    {
        return Json::decode($this->link_params);
    }

    /**
     * @param array $value
     */
    public function setLinkParams($value)
    {
        $this->link_params = Json::encode($value);
    }

    /**
     * Тайтл для ссылок в меню
     * @return string
     */
    public function getLinkTitle()
    {
        $linkParams = $this->getLinkParams();
        return empty($linkParams['title']) ? $this->title : $linkParams['title'];
    }

    /**
     * @var array
     */
    private static $_statuses = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
        self::STATUS_MAIN_PAGE => 'Main Page',
    ];

    /**
     * @return array
     */
    public static function statusLabels()
    {
        return array_map(function($label) {
            return Yii::t('gromver.platform', $label);
        }, self::$_statuses);
    }

    /**
     * @param null|integer $status
     * @return string
     */
    public function getStatusLabel($status = null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }
        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    private static $_linkTypes = [
        self::LINK_ROUTE => 'Component route',
        self::LINK_HREF => 'Link as is',
    ];

    /**
     * Список типов ссылки
     * @return array
     */
    public static function linkTypeLabels()
    {
        return array_map(function($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_linkTypes);
    }

    /**
     * Возвращает локализованную метку
     * @param null|integer $type
     * @return string
     */
    public function getLinkTypeLabel($type = null)
    {
        if ($type === null) {
            return Yii::t('gromver.platform', self::$_linkTypes[$this->link_type]);
        }
        return Yii::t('gromver.platform', self::$_linkTypes[$type]);
    }

    /**
     * @return array    [path, params]
     */
    public function parseUrl()
    {
        $arUrl = parse_url($this->link);
        parse_str(@$arUrl['query'], $params);
        if(!empty($arUrl['fragment']))
            $params['#'] = $arUrl['fragment'];
        return [trim($arUrl['path'], '/'), $params];
    }

    /**
     * @param string|array $route
     * @param null $params
     * @return mixed|null|string
     */
    public static function toRoute($route, $params = null)
    {
        if (is_array($route)) {
            $_route = $route;
            $route = ArrayHelper::remove($_route, 0);
            $params = array_merge($_route, (array)$params);
        }

        return !empty($params) ? $route . '?' . http_build_query($params) : $route;
    }

    /**
     * @var integer
     */
    private $_context;

    /**
     * @param integer $value
     */
    public function setContext($value)
    {
        $this->_context = $value;
    }

    /**
     * @return integer
     */
    public function getContext()
    {
        return $this->_context;
    }

    /**
     * @return bool
     */
    public function isProperContext()
    {
        return $this->_context === self::CONTEXT_PROPER;
    }

    /**
     * @return bool
     */
    public function isApplicableContext()
    {
        return $this->_context === self::CONTEXT_APPLICABLE;
    }

    //TranslatableInterface
    /**
     * @inheritdoc
     */
    public function getTranslations()
    {
        return self::hasMany(self::className(), ['translation_id' => 'translation_id'])->indexBy('language');
    }

    /**
     * @inheritdoc
     */
    public function getLanguage()
    {
        return $this->language;
    }


    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        if ($this->link_type == self::LINK_ROUTE) {
            return ['/' . $this->path, UrlManager::LANGUAGE_PARAM => $this->language];
        } else {
            return $this->link;
        }
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        if ($model['link_type'] == self::LINK_ROUTE) {
            return ['/' . $model['path'], UrlManager::LANGUAGE_PARAM => $model['language']];
        } else {
            return $model['link'];
        }
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/grom/menu/backend/item/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/grom/menu/backend/item/view', 'id' => $model['id']];
    }

    public function getBreadcrumbs($includeSelf = false)
    {
        if ($this->isRoot()) {
            return [];
        } else {
            $path = $this->parents()->excludeRoots()->all();
            if ($includeSelf) {
                $path[] = $this;
            }
            return array_map(function ($item) {
                /** @var self $item */
                return [
                    'label' => $item->title,
                    'url' => $item->getFrontendViewLink()
                ];
            }, $path);
        }
    }
}
