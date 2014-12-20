<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers;

use gromver\modulequery\ModuleQuery;
use gromver\platform\basic\main\models\PlatformParams;
use gromver\models\ObjectModel;
use gromver\platform\basic\menu\models\MenuTypeSearch;
use gromver\platform\basic\news\models\CategorySearch;
use gromver\platform\basic\news\models\PostSearch;
use gromver\platform\basic\page\models\PageSearch;
use gromver\platform\basic\tag\models\Tag;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use Yii;
use yii\caching\Cache;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
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
                    [
                        'allow' => true,
                        'actions' => ['params', 'flush-cache', 'mode', 'contact-gromver'],  //todo contact-gromver
                        'roles' => ['administrate'],
                    ],
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

    public function actionDesktop()
    {
        return $this->render('index', [
            'items' => ModuleQuery::instance()->implement('\gromver\platform\basic\interfaces\DesktopInterface')->orderBy('desktopOrder')->fetch('getDesktopItem')
        ]);
    }

    public function actionMode($mode, $backUrl = null) {
        $this->module->setMode($mode);

        $this->redirect($backUrl ? $backUrl : Yii::$app->request->getReferrer());
    }

    public function actionParams($modal = null)
    {
        $paramsPath = Yii::getAlias($this->module->paramsPath);
        $paramsFile = $paramsPath . DIRECTORY_SEPARATOR . 'params.php';

        $params = $this->module->params;

        $model = new ObjectModel(PlatformParams::className());
        $model->setAttributes($params);

        if($model->load(Yii::$app->request->post())) {
            if($model->validate() && Yii::$app->request->getBodyParam('task') !== 'refresh') {

                FileHelper::createDirectory($paramsPath);
                file_put_contents($paramsFile, '<?php return '.var_export($model->toArray(), true).';');

                Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Configuration saved.'));

                if ($modal) {
                    ModalIFrame::refreshPage();
                }
            }
        }

        if ($modal) {
            $this->layout = 'modal';
        } else {
            $this->layout = 'main';
        }

        return $this->render('params', [
            'model' => $model
        ]);
    }

    public function actionFlushCache($component = 'cache')
    {
        /** @var Cache $cache */
        $cache = Instance::ensure($component, Cache::className());

        $cache->flush();

        Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Cache flushed.'));

        return $this->redirect(['index']);
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
