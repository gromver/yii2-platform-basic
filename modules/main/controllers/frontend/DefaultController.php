<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers\frontend;

use gromver\platform\basic\modules\menu\models\MenuTypeSearch;
use gromver\platform\basic\modules\news\models\CategorySearch;
use gromver\platform\basic\modules\news\models\PostSearch;
use gromver\platform\basic\modules\page\models\PageSearch;
use gromver\platform\basic\modules\tag\models\Tag;
use Yii;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    // todo избавиться от select-екшенов
                    [
                        'allow' => true,
                        'actions' => ['select-page', 'select-post', 'select-category', 'select-menu', 'tag-list'],
                        'roles' => ['read'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'error', 'contact', 'captcha', 'page-not-found'],
                    ],
                ]
            ]
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => 'yii\captcha\CaptchaAction'
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionPageNotFound()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    public function actionSelectMenu()
    {
        $searchModel = new MenuTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->layout = 'modal';

        return $this->render('select-menu', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionSelectPage()
    {
        $searchModel = new PageSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->layout = 'modal';

        return $this->render('select-page', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionSelectCategory()
    {
        $searchModel = new CategorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->layout = 'modal';

        return $this->render('select-category', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionSelectPost()
    {
        $searchModel = new PostSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());

        $this->layout = 'modal';

        return $this->render('select-post', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel,
        ]);
    }

    public function actionTagList($query = null, $language = null) {
        $result = Tag::find()->select('id AS value, title AS text, group AS optgroup')->filterWhere(['like', 'title', urldecode($query)])->andFilterWhere(['language' => $language])->limit(20)->asArray()->all();

        echo Json::encode($result);
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}
