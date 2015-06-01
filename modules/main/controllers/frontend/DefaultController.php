<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\controllers\frontend;


use Yii;
use yii\filters\AccessControl;
use yii\web\NotFoundHttpException;

/**
 * Class DefaultController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class DefaultController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'contact', 'page-not-found', 'dummy-page'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Приветсвенная страница
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionPageNotFound()
    {
        throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
    }

    /**
     * Форма контактов
     * @return string
     */
    public function actionContact()
    {
        return $this->render('contact');
    }

    /**
     * Временная страница, используется в качестве заглушки при создании пунктов меню
     * @return string
     */
    public function actionDummyPage()
    {
        return $this->render('dummyPage');
    }
}
