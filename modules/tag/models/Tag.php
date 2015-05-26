<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\tag\models;


use dosamigos\transliterator\TransliteratorHelper;
use gromver\platform\basic\components\UrlManager;
use gromver\platform\basic\interfaces\model\TranslatableInterface;
use gromver\platform\basic\interfaces\model\ViewableInterface;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Inflector;

/**
 * This is the model class for table "grom_tag".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property integer $translation_id
 * @property string $language
 * @property string $title
 * @property string $alias
 * @property integer $status
 * @property string $group
 * @property string $metakey
 * @property string $metadesc
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 * @property integer $hits
 * @property integer $lock
 *
 * @property TagToItem[] $tagToItems
 * @property Tag[] $translations
 */
class Tag extends \yii\db\ActiveRecord implements ViewableInterface, TranslatableInterface
{
    const STATUS_PUBLISHED = 1;
    const STATUS_UNPUBLISHED = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_tag}}';
    }

    /**
     * @return string
     */
    public static function pivotTableName()
    {
        return '{{%grom_tag_to_item}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['language', 'title', 'status'], 'required'],
            [['status', 'created_at', 'updated_at', 'created_by', 'updated_by', 'hits', 'lock'], 'integer'],
            [['language'], 'string', 'max' => 7],
            [['title'], 'string', 'max' => 100],
            [['group'], 'filter', 'filter' => function($value) {
                // если во вьюхе используется select2, отфильтровываем значение из массива [0 => 'значение'] -> 'значение'
                return is_array($value) ? reset($value) : $value;
            }],
            [['alias', 'group', 'metakey'], 'string', 'max' => 255],
            [['metadesc'], 'string', 'max' => 2048],

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
                $query->andWhere(['language' => $this->language]);
            }],
            [['alias'], 'required', 'enableClientValidation' => false],
            [['translation_id'], 'unique', 'filter' => function($query) {
                /** @var $query \yii\db\ActiveQuery */
                $query->andWhere(['language' => $this->language]);
            }, 'message' => Yii::t('gromver.platform', 'Локализация ({language}) для записи (ID:{id}) уже существует.', ['language' => $this->language, 'id' => $this->translation_id])],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'translation_id' => Yii::t('gromver.platform', 'Translation ID'),
            'language' => Yii::t('gromver.platform', 'Language'),
            'title' => Yii::t('gromver.platform', 'Title'),
            'alias' => Yii::t('gromver.platform', 'Alias'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'group' => Yii::t('gromver.platform', 'Group'),
            'metakey' => Yii::t('gromver.platform', 'Meta keywords'),
            'metadesc' => Yii::t('gromver.platform', 'Meta description'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'created_by' => Yii::t('gromver.platform', 'Created By'),
            'updated_by' => Yii::t('gromver.platform', 'Updated By'),
            'hits' => Yii::t('gromver.platform', 'Hits'),
            'lock' => Yii::t('gromver.platform', 'Lock'),
        ];
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
        ];
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        if ($insert && $this->translation_id === null) {
            $this->updateAttributes([
                'translation_id' => $this->id
            ]);
        }

        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        parent::afterDelete();

        $this->getDb()->createCommand()->delete(self::pivotTableName(), ['tag_id' => $this->id])->execute();
    }

    private static $_statuses = [
        self::STATUS_PUBLISHED => 'Published',
        self::STATUS_UNPUBLISHED => 'Unpublished',
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
     * @param string|null $status
     * @return string
     */
    public function getStatusLabel($status = null)
    {
        return Yii::t('gromver.platform', self::$_statuses[$status === null ? $this->status : $status]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTagToItems()
    {
        return $this->hasMany(TagToItem::className(), ['tag_id' => 'id']);
    }

    /**
     * @inheritdoc
     */
    public function optimisticLock()
    {
        return 'lock';
    }

    /**
     * Увеличивает счетчик просмотров
     * @return int
     */
    public function hit()
    {
        // todo любую статистику приложения собирать через логи
        return 1;//return $this->updateAttributes(['hits' => $this->hits + 1]);
    }

    //http://www.slideshare.net/edbond/tagging-and-folksonomy-schema-design-for-scalability-and-performance?related=2

    /**
     * @param $tagText
     * @param null $coverage
     * @return Query
     */
    public static function relatedTagsToTagQuery($tagText, $coverage = null){
        $relatedItemsQuery = (new Query())
            ->select('item_id, item_class')
            ->from(self::tableName() . 't')->innerJoin(self::pivotTableName() . ' t2i', 't.id=t2i.tag_id')->where(['title'=>$tagText]);

        if($coverage) $relatedItemsQuery->limit($coverage);

        $resultQuery = (new ActiveQuery(self::className()))->select('t2.*')->from(['t2i1' => $relatedItemsQuery])
            ->innerJoin(self::pivotTableName() . ' t2i2', 't2i1.item_id=t2i2.item_id AND t2i1.item_class=t2i2.item_class')
            ->innerJoin(self::tableName() . ' t2', 't2i2.tag_id=t2.id')
            ->groupBy('t2i2.tag_id');

        return $resultQuery;
    }

    /**
     * @param $itemId
     * @param $itemClass
     * @return ActiveQuery
     */
    public static function relatedItemsToItemQuery($itemId, $itemClass)
    {
        return TagToItem::find()
            ->select('p2.*')
            ->from(self::pivotTableName() . ' p1')
            ->innerJoin(self::pivotTableName() . ' p2', 'p1.tag_id=p2.tag_id')
            ->where(['p1.item_id' => $itemId, 'p1.item_class' => $itemClass])
            ->groupBy('p2.item_id');
    }
    /**
     * @param $tags
     * @return ActiveQuery
     */
    public static function relatedItemsToTagsQuery($tags)
    {
        $tags = (array)$tags;

        $query = TagToItem::find()
            ->select('t2i0.*')
            ->from(self::tableName() . ' t0')
            ->innerJoin(self::pivotTableName() . ' t2i0', 't0.id=t2i0.tag_id');

        foreach($tags as $k => $tag) {
            if($k) {
                $query->join('CROSS JOIN', self::tableName() . ' t'.$k);
                $query->innerJoin(self::pivotTableName() . ' t2i'.$k, 't2i'.($k-1).'.item_id=t2i'.$k.'.item_id AND t2i'.($k-1).'.item_class=t2i'.$k.'.item_class AND t'.$k.'.id=t2i'.$k.'.tag_id');
            }

            $query->andWhere("t{$k}.title=:title{$k}", [":title{$k}"=>$tag]);
        }

        return $query;
    }

    /**
     * @param $tags
     * @return ActiveQuery
     */
    public static function relatedItemsToTagsOrQuery($tags)
    {
        return TagToItem::find()
            ->select('item_id, item_class')
            ->from(self::tableName(). ' t')
            ->innerJoin(self::pivotTableName() . ' t2i', 't.id=t2i.tag_id')
            ->where(['t.title'=>$tags])
            ->groupBy('item_id');
    }

    // ViewableInterface
    /**
     * @inheritdoc
     */
    public function getFrontendViewLink()
    {
        return ['/grom/tag/frontend/default/view', 'id' => $this->id, 'alias' => $this->alias, UrlManager::LANGUAGE_PARAM => $this->language];
    }

    /**
     * @inheritdoc
     */
    public static function frontendViewLink($model)
    {
        return ['/grom/tag/frontend/default/view', 'id' => $model['id'], 'alias' => $model['alias'], UrlManager::LANGUAGE_PARAM => $model['language']];
    }

    /**
     * @inheritdoc
     */
    public function getBackendViewLink()
    {
        return ['/grom/tag/backend/default/view', 'id' => $this->id];
    }

    /**
     * @inheritdoc
     */
    public static function backendViewLink($model)
    {
        return ['/grom/tag/backend/default/view', 'id' => $model['id']];
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
}
