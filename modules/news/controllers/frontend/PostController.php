<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\news\controllers\frontend;


use gromver\platform\basic\modules\news\models\Category;
use gromver\platform\basic\modules\news\models\Post;
use gromver\platform\basic\modules\main\models\DbState;
use yii\data\ActiveDataProvider;
use yii\helpers\StringHelper;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Yii;
use Zelenin\yii\extensions\Rss\RssView;

/**
 * Class PostController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PostController extends \yii\web\Controller
{
    public $defaultAction = 'view';

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\filters\HttpCache',
                'only' => ['rss'],
                'lastModified' => function () {
                        return DbState::timestamp('{{%grom_post}}');
                    },
            ],
        ];
    }

    public function actionIndex($category_id = null, $tag_id = null)
    {
        return $this->render('index', [
            'categoryId' => $category_id,
            'tagId' => $tag_id
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->loadModel($id)
        ]);
    }

    public function actionDay($year, $month, $day, $category_id = null)
    {
        return $this->render('day', [
            'model' => $category_id ? $this->loadCategoryModel($category_id) : null,
            'year' => $year,
            'month' => $month,
            'day' => $day
        ]);
    }

    public function actionRss($category_id = null)
    {
        return RssView::widget([
            'dataProvider' => new ActiveDataProvider([
                    'query' => Post::find()->published()->category($category_id)->language(Yii::$app->language)->orderBy(['published_at' => SORT_DESC]),
                    'pagination' => [
                        'pageSize' => $this->module->rssPageSize
                    ],
                ]),
            'channel' => [
                'title' => Yii::$app->grom->siteName,
                'link' => Url::toRoute(['', 'category_id' => $category_id], true),
                'description' => $category_id ? $this->loadCategoryModel($category_id)->title : Yii::t('gromver.platform', 'All news'),
                'language' => Yii::$app->language
            ],
            'items' => [
                'title' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return $model->title;
                    },
                'description' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return $model->preview_text ? $model->preview_text : StringHelper::truncateWords(strip_tags($model->detail_text), 40);
                    },
                'link' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return Url::toRoute($model->getFrontendViewLink(), true);
                    },
                'author' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return $model->user->email . ' (' . $model->user->username . ')';
                    },
                'guid' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return Url::toRoute($model->getFrontendViewLink(), true) . ' ' . Yii::$app->formatter->asDatetime($model->updated_at, 'php:'.DATE_RSS);
                    },
                'pubDate' => function ($model) {
                        /** @var $model \gromver\platform\basic\modules\news\models\Post */
                        return Yii::$app->formatter->asDatetime($model->published_at, 'php:'.DATE_RSS);
                    }
            ]
        ]);
    }

    public function loadModel($id)
    {
        if(!($model = Post::findOne($id))) {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested post does not exist.'));
        }

        return $model;
    }

    public function loadCategoryModel($id)
    {
        if(!($model = Category::findOne($id))) {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested category does not exist.'));
        }

        return $model;
    }
}
