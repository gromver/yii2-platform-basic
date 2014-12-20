<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model gromver\platform\basic\modules\menu\models\MenuItem */
/* @var $sourceModel gromver\platform\basic\modules\menu\models\MenuItem */
/* @var $linkParamsModel gromver\platform\basic\modules\menu\models\MenuLinkParams */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="menu-form">

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
        ]); ?>

    <?= $form->errorSummary([$model, $linkParamsModel]) ?>

    <ul class="nav nav-tabs">
        <li class="active"><a href="#main" data-toggle="tab"><?= Yii::t('gromver.platform', 'Main') ?></a></li>
        <li><a href="#link-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Link params') ?></a></li>
        <li><a href="#meta-options" data-toggle="tab"><?= Yii::t('gromver.platform', 'Metadata') ?></a></li>
    </ul>
    <br/>
    <div class="tab-content">
        <div id="main" class="tab-pane active">
            <?= $form->field($model, 'menu_type_id')->dropDownList(['' => Yii::t('gromver.platform', 'Select ...')] + \yii\helpers\ArrayHelper::map(\gromver\platform\basic\modules\menu\models\MenuType::find()->all(),'id', 'title'), ['id' => 'menu_type_id']) ?>

            <?= $form->field($model, 'language')->dropDownList(Yii::$app->getLanguagesList(), ['prompt' => Yii::t('gromver.platform', 'Select ...'), 'id' => 'language']) ?>

            <?= $form->field($model, 'parent_id')->widget(\kartik\widgets\DepDrop::className(), [
                'pluginOptions'=>[
                    'depends' => ['menu_type_id', 'language'],
                    'placeholder' => Yii::t('gromver.platform', 'Select ...'),
                    'url' => \yii\helpers\Url::to(['type-items', 'update_item_id' => $model->isNewRecord ? null : $model->id, 'selected' => $model->parent_id]),
                ]
            ]) ?>

            <?= $form->field($model, 'title')->textInput(['maxlength' => 1024, 'placeholder' => isset($sourceModel) ? $sourceModel->title : null]) ?>

            <?= $form->field($model, 'alias')->textInput(['maxlength' => 255, 'placeholder' => Yii::t('gromver.platform', 'Auto-generate')]) ?>

            <?= $form->field($model, 'status')->dropDownList(['' => Yii::t('gromver.platform', 'Select ...')] + $model->statusLabels()) ?>

            <?//= $form->field($model, 'path')->textInput(['maxlength' => 2048]) ?>

            <?= $form->field($model, 'link_type')->dropDownList($model->getLinkTypes()) ?>

            <?php $this->registerJs("$('#" . Html::getInputId($model, 'link_type') . "').change(function (event){
                if($(this).val() === '" . \gromver\platform\basic\modules\menu\models\MenuItem::LINK_ROUTE . "') {
                    $('#router-button a').attr('href', " . \yii\helpers\Json::encode(\yii\helpers\Url::toRoute(['routers'])) . ")
                } else {
                    $('#router-button a').attr('href', " . \yii\helpers\Json::encode(\yii\helpers\Url::toRoute(['select'])) . ")
                }
            }).change()") ?>

            <?php
            $linkLabel = Html::activeLabel($model, 'link');
            $linkInputId = Html::getInputId($model, 'link');

            echo $form->field($model, 'link', [
                    'template' => "{label}\n{beginWrapper}\n<div class=\"input-group\">{input}{controls}</div>\n{error}\n{endWrapper}\n{hint}",
                    'parts' => [
                        '{controls}' => \gromver\widgets\ModalIFrame::widget([
                                'id' => 'router',
                                'modalOptions' => [
                                    'header' => $linkLabel,
                                    'size' => \yii\bootstrap\Modal::SIZE_LARGE,
                                ],
                                'buttonOptions' => [
                                    'tag' => 'span',
                                    'class' => 'input-group-btn'
                                ],
                                'buttonContent' => Html::a('<span class="glyphicon glyphicon-folder-open"></span>', ['routers'], ['class'=>'btn btn-default']),
                                'iframeHandler' => "function(data){
                                    $('#{$linkInputId}').val(data.link)
                                }",
                            ])
                    ]
                ])->textInput(['maxlength' => 1024]) ?>

            <?= $form->field($model, 'secure')->dropDownList([
                    '' => 'No',
                    '1' => 'Yes'
                ]) ?>

            <?= $form->field($model, 'ordering')->textInput() ?>

            <?= $form->field($model, 'layout_path')->textInput(['maxlength' => 1024]) ?>

            <?= $form->field($model, 'access_rule')->dropDownList(\yii\helpers\ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name'), [
                    'prompt' => 'Не выбрано'
                ]) ?>

        </div>

        <div id="link-options" class="tab-pane">
            <?= $form->field($linkParamsModel, 'title')->textInput() ?>

            <?= $form->field($linkParamsModel, 'class')->textInput() ?>

            <?= $form->field($linkParamsModel, 'style')->textInput() ?>

            <?= $form->field($linkParamsModel, 'target')->textInput() ?>

            <?= $form->field($linkParamsModel, 'onclick')->textInput() ?>

            <?= $form->field($linkParamsModel, 'rel')->textInput() ?>
        </div>

        <div id="meta-options" class="tab-pane">
            <?= $form->field($model, 'note')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'metakey')->textInput(['maxlength' => 255]) ?>

            <?= $form->field($model, 'metadesc')->textarea(['maxlength' => 2048]) ?>

            <?= $form->field($model, 'robots')->dropDownList([
                    'index, follow' => 'Index, Follow',
                    'noindex, follow' => 'No index, follow',
                    'index, nofollow' => 'Index, No follow',
                    'noindex, nofollow' => 'No index, no follow'
                ], [
                    'prompt' => 'Не выбрано'
                ]) ?>
        </div>
    </div>

    <?= Html::activeHiddenInput($model, 'lock') ?>

    <div>
        <?= Html::submitButton($model->isNewRecord ? ('<i class="glyphicon glyphicon-plus"></i> ' . Yii::t('gromver.platform', 'Create')) : ('<i class="glyphicon glyphicon-pencil"></i> ' . Yii::t('gromver.platform', 'Update')), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php $this->registerJs('$("#menu_type_id").change()', \yii\web\View::POS_READY);