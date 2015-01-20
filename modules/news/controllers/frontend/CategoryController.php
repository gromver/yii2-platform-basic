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
use yii\web\NotFoundHttpException;
use Yii;

/**
 * Class CategoryController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class CategoryController extends \yii\web\Controller
{
    public $defaultAction = 'view';

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->loadModel($id),
        ]);
    }

    public function actionCategories($id)
    {
        return $this->render('categories', [
            'model' => $this->loadModel($id),
        ]);
    }

    public function actionPosts($id)
    {
        return $this->render('posts', [
            'model' => $this->loadModel($id),
        ]);
    }

    public function loadModel($id)
    {
        if(!($model = Category::findOne($id))) {
            throw new NotFoundHttpException(Yii::t('gromver.platform', 'The requested category does not exist.'));
        }

        return $model;
    }
}
