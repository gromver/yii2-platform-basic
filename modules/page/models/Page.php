<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\page\models;


use dosamigos\transliterator\TransliteratorHelper;
use gromver\platform\basic\behaviors\NestedSetsBehavior;
use gromver\platform\basic\behaviors\SearchBehavior;
use gromver\platform\basic\behaviors\TaggableBehavior;
use gromver\platform\basic\behaviors\VersionBehavior;
use gromver\platform\basic\components\UrlManager;
use gromver\platform\basic\interfaces\model\SearchableInterface;
use gromver\platform\basic\interfaces\model\TranslatableInterface;
use gromver\platform\basic\interfaces\model\ViewableInterface;
use gromver\platform\basic\modules\user\models\User;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * This is the model class for table "grom_page".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $parent_id
 * @property integer $translation_id
 * @property string $language
 * @property string $title
 * @property string $alias
 * @property string $path
 * @property string $preview_text
 * @property string $detail_text
 * @property string $metakey
 * @property string $metadesc
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $status
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $lft
 * @property integer $rgt
 * @property integer $level
 * @property integer $ordering
 * @property integer $hits
 * @property integer $lock
 *
 * @property User $user
 * @property Page[] $translations
 * @property Page $parent
 * @property \gromver\platform\basic\modules\tag\models\Tag[] $tags
 */
class Page extends \yii\db\ActiveRecord implements TranslatableInterface, ViewableInterface, SearchableInterface
{
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_page}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'title', 'detail_text', 'status'], 'required'],
            [['preview_text', 'detail_text'], 'string'],
            [['parent_id', 'created_at', 'updated_at', 'status', 'created_by', 'updated_by', 'lft', 'rgt', 'level', 'ordering', 'hits', 'lock'], 'integer'],
            [['language'], 'string', 'max' => 7],
            [['title'], 'string', 'max' => 1024],
            [['alias', 'metakey'], 'string', 'max' => 255],
            [['metadesc'], 'string', 'max' => 2048],

            [['parent_id'], 'integer'],
            [['parent_id'], 'filter', 'filter' => 'intval'],
            [['parent_id'], 'exist', 'targetAttribute' => 'id'],
            [['parent_id'], 'compare', 'compareAttribute' => 'id', 'operator'=>'!='],

            [['alias'], 'filter', 'filter' => 'trim'],
            [['alias'], 'filter', 'filter' => function($value){
                    if (empty($value)) {
                        return Inflector::slug(TransliteratorHelper::process($this->title));
                    } else {
                        return Inflector::slug($value);
                    }
                }],
            [['alias'], 'unique', 'filter' => function($query){
                /** @var $query \yii\db\ActiveQuery */
                //$query->andWhere(['language' => $this->language]);
                /** @var $query \yii\db\ActiveQuery */
                if($parent = self::findOne($this->parent_id)){
                    $query->andWhere('lft>=:lft AND rgt<=:rgt AND level=:level AND language=:language', [
                        'lft' => $parent->lft,
                        'rgt' => $parent->rgt,
                        'level' => $parent->level + 1,
                        'language' => $this->language
                    ]);
                }
            }],
            [['alias'], 'string', 'max' => 250],
            [['alias'], 'required', 'enableClientValidation' => false],
            [['translation_id'], 'unique', 'filter' => function($query) {
                /** @var $query \yii\db\ActiveQuery */
                $query->andWhere(['language' => $this->language]);
            }, 'message' => Yii::t('gromver.platform', 'Локализация ({language}) для записи (ID:{id}) уже существует.', ['language' => $this->language, 'id' => $this->translation_id])],
            [['tags', 'versionNote', 'lock'], 'safe'],
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
            'parent_id' => Yii::t('gromver.platform', 'Parent'),
            'translation_id' => Yii::t('gromver.platform', 'Translation ID'),
            'language' => Yii::t('gromver.platform', 'Language'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'alias' => Yii::t('gromver.platform', 'Alias'),
            'path' => Yii::t('gromver.platform', 'Path'),
            'preview_text' => Yii::t('gromver.platform', 'Preview Text'),
            'detail_text' => Yii::t('gromver.platform', 'Detail Text'),
            'metakey' => Yii::t('gromver.platform', 'Meta keywords'),
            'metadesc' => Yii::t('gromver.platform', 'Meta description'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
            'updated_by' => Yii::t('gromver.platform', 'Updated By'),
            'lft' => Yii::t('gromver.platform', 'Lft'),
            'rgt' => Yii::t('gromver.platform', 'Rgt'),
            'level' => Yii::t('gromver.platform', 'Level'),
            'ordering' => Yii::t('gromver.platform', 'Ordering'),
            'hits' => Yii::t('gromver.platform', 'Hits'),
            'lock' => Yii::t('gromver.platform', 'Lock'),
            'tags' => Yii::t('gromver.platform', 'Tags'),
            'versionNote' => Yii::t('gromver.platform', 'Version Note')
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            TaggableBehavior::className(),
            SearchBehavior::className(),
            NestedSetsBehavior::className(),
            [
                'class' => VersionBehavior::className(),//todo затестить чекаут в версию с уже занятым алиасом
                'attributes' => ['title', 'alias', 'preview_text', 'detail_text', 'metakey', 'metadesc']
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    /**
     * @inheritdoc
     * @return PageQuery
     */
    public static function find()
    {
        return new PageQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    private static $_statuses = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
    ];

    public static function statusLabels()
    {
        return array_map(function($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_statuses);
    }

    public function getStatusLabel($status=null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }
        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    public function optimisticLock()
    {
        return 'lock';
    }

    public function hit()
    {
        return $this->updateAttributes(['hits' => $this->hits + 1]);
    }

    public function saveNode($runValidation = true, $attributes = null)
    {
        if ($this->getIsNewRecord()) {
            if($parent = self::findOne($this->parent_id)) {
                return $this->appendTo($parent, $runValidation, $attributes);
            } else {
                return $this->makeRoot($parent, $runValidation, $attributes);
            }
        }
        // категория перемещена в другую категорию
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

        // нормализуем пути подкатегорий для текущей категории при её перемещении либо изменении псевдонима
        if (array_key_exists('parent_id', $changedAttributes) || array_key_exists('alias', $changedAttributes)) {
            $this->refresh();
            $this->normalizePath();
        }

        // ранжируем категории ели нужно
        if (array_key_exists('ordering', $changedAttributes)) {
            $this->ordering ? $this->parent->reorderNode('ordering') : $this->parent->reorderNode('lft');
        }
    }

    private function calculatePath()
    {
        $aliases = $this->parents()->noRoots()->select('alias')->column();
        return empty($aliases) ? $this->alias : implode('/', $aliases) . '/' . $this->alias;
    }

    public function normalizePath($parentPath = null)
    {
        if($parentPath === null) {
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

    /**
     * @return PageQuery
     */
    public function getParent()
    {
        return $this->hasOne(self::className(), ['id' => 'parent_id']);
    }

    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/grom/page/frontend/default/view', 'id' => $this->id, 'alias' => $this->alias, UrlManager::LANGUAGE_PARAM => $this->language];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/grom/page/frontend/default/view', 'id' => $model['id'], 'alias' => $model['alias'], UrlManager::LANGUAGE_PARAM => $model['language']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/grom/page/backend/default/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/grom/page/backend/default/view', 'id' => $model['id']];
    }

    //TranslatableInterface
    public function getTranslations()
    {
        return self::hasMany(self::className(), ['translation_id' => 'translation_id'])->andWhere(['!=', 'language', $this->language])->indexBy('language');
    }

    public function getLanguage()
    {
        return $this->language;
    }

    public function extraFields()
    {
        return [
            'published',
            'tags' => function($model) {
                    return array_values(array_map(function($tag) {
                        return $tag->title;
                    }, $model->tags));
                },
            'text' => function($model) {
                    /** @var self $model */
                    return strip_tags($model->preview_text . ' ' . $model->detail_text);
                },
            'date' => function($model) {
                    /** @var self $model */
                    return date(DATE_ISO8601, $model->updated_at);
                },
        ];
    }

    public function isPublished()
    {
        return $this->status == self::STATUS_PUBLISHED;
    }

    public function getBreadcrumbs($includeSelf = false)
    {
        if ($this->isRoot()) {
            return [];
        } else {
            $path = $this->parents()->noRoots()->all();
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

    // SearchableInterface
    /**
     * @inheritdoc
     */
    public function getSearchTitle()
    {
        return $this->title;
    }

    /**
     * @inheritdoc
     */
    public function getSearchContent()
    {
        return $this->detail_text;
    }

    /**
     * @inheritdoc
     */
    public function getSearchTags()
    {
        return ArrayHelper::map($this->tags, 'id', 'title');
    }

    // SqlSearch integration
    /**
     * @param $query \yii\db\ActiveQuery
     * @param $widget \gromver\platform\basic\widgets\SearchResultsSql
     */
    static public function sqlBeforeSearch($query, $widget)
    {
        if ($widget->frontendMode) {
            $query->leftJoin('{{%grom_page}}', [
                    'AND',
                    ['=', 'model_class', self::className()],
                    'model_id={{%grom_page}}.id',
                    ['NOT IN', '{{%grom_page}}.parent_id', Page::find()->unpublished()->select('{{%grom_page}}.id')->column()]
                ]
            )->addSelect('{{%grom_page}}.id')
                ->andWhere('model_class=:pageClassName XOR {{%grom_page}}.id IS NULL', [':pageClassName' => self::className()]);
        }
    }

    // ElasticSearch integration
    /**
     * @param $query \yii\elasticsearch\ActiveQuery
     * @param $widget \gromver\platform\basic\widgets\SearchResultsElasticsearch
     */
    static public function elasticsearchBeforeSearch($query, $widget)
    {
        if ($widget->frontendMode) {
            $widget->filters[] = [
                'not' => [
                    'and' => [
                        [
                            'term' => ['model_class' => self::className()]
                        ],
                        [
                            'terms' => ['model_id' => self::find()->unpublished()->select('{{%grom_page}}.id')->column()]
                        ],
                    ]
                ]
            ];
        }
    }
}
