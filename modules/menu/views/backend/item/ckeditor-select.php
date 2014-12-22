<?php

/**
 * @var yii\web\View $this
 * @var string $CKEditor
 * @var string $CKEditorFuncNum
 * @var string $langCode
 */

?>
<h3><?= Yii::t('gromver.platform', 'Link Type') ?></h3>
<ul class="nav nav-pills nav-stacked">
    <li><a href="<?= \yii\helpers\Url::toRoute(['ckeditor-select-component', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('gromver.platform', 'Components') ?></a></li>
    <li><a href="<?= \yii\helpers\Url::toRoute(['ckeditor-select-menu', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('gromver.platform', 'Menu Items') ?></a></li>
    <li><a href="<?= \yii\helpers\Url::toRoute(['/grom/media/manager/manager', 'CKEditor' => $CKEditor, 'CKEditorFuncNum' => $CKEditorFuncNum, 'langCode' => $langCode]) ?>"><?= Yii::t('gromver.platform', 'Media Manager') ?></a></li>
</ul>
