<?php

/**
 * @var yii\web\View $this
 * @var string $CKEditor
 * @var string $CKEditorFuncNum
 * @var string $langCode
 */

$iframeId = 'iframe-select';

$this->registerJs("$(document).on('data', '#{$iframeId}', function(e, data){
    window.opener.CKEDITOR.tools.callFunction({$CKEditorFuncNum}, data.link);
    window.close();
})");
?>
<style>
    body {
        padding: 15px 0;
        height: 100%;
    }
    body > .container-fluid {
        height: 100%;
    }
</style>

<iframe src="<?= \yii\helpers\Url::toRoute(['select']) ?>" id="<?= $iframeId ?>" name="<?= $iframeId ?>" width="100%" height="100%" style="border: none; padding: 0; margin: 0;"></iframe>
