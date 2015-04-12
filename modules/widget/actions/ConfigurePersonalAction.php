<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\widget\actions;


use gromver\models\ObjectModel;
use gromver\platform\basic\modules\widget\models\WidgetConfigPersonal;
use gromver\widgets\ModalIFrame;
use Yii;
use yii\helpers\Json;
use yii\web\BadRequestHttpException;

/**
 * Class ConfigureAction
 * Экшен для настройки персональных виджетов.
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ConfigurePersonalAction extends \yii\base\Action
{
    public $view = '@gromver/platform/basic/modules/widget/views/actions/configurePersonal/form';

    public function run($modal=null) {
        if (!($widget_id = Yii::$app->request->getBodyParam('widget_id'))) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget ID isn't specified"));
        }

        if (!($widget_class = Yii::$app->request->getBodyParam('widget_class'))) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget Class isn't specified"));
        }

        if (($widget_context = Yii::$app->request->getBodyParam('widget_context'))===null) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget Context isn't specified"));
        }

        $selected_context = Yii::$app->request->getBodyParam('selected_context', $widget_context);

        $task = Yii::$app->request->getBodyParam('task');

        if (($url = Yii::$app->request->getBodyParam('url'))===null) {
            throw new BadRequestHttpException(Yii::t('gromver.platform', "Widget page url isn't specified"));
        }
        //$url = Yii::$app->request->getBodyParam('url', Yii::$app->request->getReferrer());

        if ($task == 'delete') {
            if (Yii::$app->request->getBodyParam('bulk-method')) {
                foreach (WidgetConfigPersonal::find()->where('widget_id=:widget_id AND context>=:context AND language=:language', [
                    ':widget_id' => $widget_id,
                    ':context' => $selected_context,
                    ':language' => Yii::$app->language
                ])->each() as $configModel) {
                    $configModel->delete();
                }
            } elseif ($configModel = WidgetConfigPersonal::findOne([
                'widget_id' => $widget_id,
                'context' => $selected_context,
                'language' => Yii::$app->language
            ])) {
                $configModel->delete();
            }

            if ($modal) {
                ModalIFrame::refreshParent();
            }
        }

        $widget_config = Yii::$app->request->getBodyParam('widget_config', '[]');
        $widgetConfig = Json::decode($widget_config);
        $widgetConfig['id'] = $widget_id;
        $widgetConfig['context'] = $selected_context;
        $widgetConfig['skipInit'] = true;
        /** @var \gromver\platform\basic\widgets\Widget $widget */
        $widget = new $widget_class($widgetConfig);

        $model = new ObjectModel($widget);

        if (($task == 'save' || $task == 'refresh') && $model->load(Yii::$app->request->post())) {
            if ($model->validate() && $task=='save') {
                $configModel = WidgetConfigPersonal::findOne([
                    'widget_id' => $widget_id,
                    'context' => $selected_context,
                    'language' => Yii::$app->language
                ]) or $configModel = new WidgetConfigPersonal;

                $configModel->loadDefaultValues();
                $configModel->widget_id = $widget_id;
                $configModel->widget_class = $widget_class;
                $configModel->context = $selected_context;
                $configModel->url = $url;
                $configModel->setParamsArray($model->toArray());

                $configModel->save();

                if (Yii::$app->request->getBodyParam('bulk-method')) {
                    foreach (WidgetConfigPersonal::find()->where('widget_id=:widget_id AND context>:context AND language=:language', [
                        ':widget_id' => $widget_id,
                        ':context' => $selected_context,
                        ':language' => Yii::$app->language
                    ])->each() as $configModel) {
                        /** @var $configModel WidgetConfigPersonal */
                        $configModel->delete();
                    }
                }

                if ($modal) {
                    ModalIFrame::refreshParent();
                } else {
                    return $this->redirect($url);
                }
            }
        }

        if($modal) {
            Yii::$app->grom->applyModalLayout();
        }

        return $this->controller->render($this->view, [
            'model' => $model,
            'widget' => $widget,
            'widget_id' => $widget_id,
            'widget_class' => $widget_class,
            'widget_config' => $widget_config,
            'widget_context' => $widget_context,
            'selected_context' => $selected_context,
            'url' => $url,
        ]);
    }
} 