<?php
/**
 * @link https://github.com/gromver/yii2-cmf.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-grom/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers\backend;


use gromver\platform\basic\modules\main\models\PlatformParams;
use gromver\models\ObjectModel;
use gromver\widgets\ModalIFrame;
use kartik\widgets\Alert;
use yii\caching\Cache;
use yii\di\Instance;
use yii\filters\AccessControl;
use yii\helpers\FileHelper;
use Yii;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \gromver\platform\basic\components\BackendController
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['params', 'flush-cache', 'flush-assets', 'mode'],  //todo contact-gromver
                        'roles' => ['administrator'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'contact', 'contact-gromver'],
                        'roles' => ['administrate'],
                    ],
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
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

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate() && Yii::$app->request->getBodyParam('task') !== 'refresh') {

                FileHelper::createDirectory($paramsPath);
                try {
                    file_put_contents($paramsFile, '<?php return ' . var_export($model->toArray(), true) . ';');
                    @chmod($paramsFile, 0777);

                    Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Configuration saved.'));

                    if ($modal) {
                        ModalIFrame::refreshParent();
                    }
                } catch (\Exception $e) {
                    Yii::$app->session->setFlash(Alert::TYPE_DANGER, $e->getMessage());
                }
            }
        }

        if ($modal) {
            Yii::$app->grom->applyModalLayout();
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

    public function actionFlushAssets()
    {
        $assetsPath = Yii::getAlias(Yii::$app->assetManager->basePath);

        if (!($handle = opendir($assetsPath))) {
            return;
        }
        while (($file = readdir($handle)) !== false) {
            if ($file === '.' || $file === '..') {
                continue;
            }
            $path = $assetsPath . DIRECTORY_SEPARATOR . $file;
            if (is_dir($path)) {
                FileHelper::removeDirectory($path);
            }
        }
        closedir($handle);

        Yii::$app->session->setFlash(Alert::TYPE_SUCCESS, Yii::t('gromver.platform', 'Assets flushed.'));

        return $this->redirect(['index']);
    }

    public function actionContact()
    {
        return $this->render('contact');
    }
}
