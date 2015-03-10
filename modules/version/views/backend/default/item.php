<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
 * @var \yii\db\ActiveRecord $item
 */

$this->title = Yii::t('gromver.platform', 'Item Versions');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="history-index">

	<h3><?= Html::encode($this->title) ?></h3>

    <div class="btn-group pull-right">
        <?=Html::label('<i class="glyphicon glyphicon-open"></i> ' . Yii::t('gromver.platform', 'Restore'), null, ['id' => 'restore-button', 'class' => 'btn btn-default', 'data' => ['toggle'=>"tooltip", 'url' => Url::toRoute(['restore'])], 'title' => 'tooltip']) ?>
        <?=Html::label('<i class="glyphicon glyphicon-eye-open"></i> ' . Yii::t('gromver.platform', 'Preview'), null, ['id' => 'preview-button', 'class' => 'btn btn-default', 'data' => ['toggle' => "tooltip", 'url' => Url::toRoute(['preview'])], 'title' => 'tooltip']) ?>
        <?=Html::label('<i class="glyphicon"></i> ' . Yii::t('gromver.platform', 'Compare'), null, ['id' => 'compare-button', 'class' => 'btn btn-default', 'data' => ['toggle' => "tooltip", 'url' => Url::toRoute(['compare'])], 'title' => 'tooltip']) ?>
        <?=Html::label('<i class="glyphicon glyphicon-lock"></i> ' . Yii::t('gromver.platform', 'Keep On/Off'), null, ['id' => 'keep-button', 'class' => 'btn btn-default', 'data' => ['toggle' => "tooltip", 'url' => Url::toRoute(['bulk-keep-forever'])], 'title' => 'tooltip']) ?>
        <?=Html::label('<i class="glyphicon glyphicon-trash"></i> ' . Yii::t('gromver.platform', 'Delete'), null, ['id' => 'delete-button', 'class' => 'btn btn-default', 'data' => ['toggle' => "tooltip", 'url' => Url::toRoute(['bulk-delete'])], 'title' => 'tooltip']) ?>
    </div>

    <?php \yii\bootstrap\BootstrapPluginAsset::register($this);

    $this->registerJs(
<<<JS
+function($){
    $('#restore-button').on('click', function(e){
        var selected = $('#table-grid').yiiGridView('getSelectedRows')
        if(selected.length != 1) alert('Please select one version.')
        else {
            $.post($(this).data('url')+'?id='+selected[0], function(response){
                parent.location.href = parent.location.href;
            })
        }
    })
    $('#preview-button').on('click', function(e){
        var selected = $('#table-grid').yiiGridView('getSelectedRows')
        if(selected.length != 1) alert('Please select one version.')
        else {
            parent.open($(this).data('url')+'?id='+selected[0],'',"width=1000,height=600,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no")//.focus()
        }
    })
    $('#compare-button').on('click', function(e){
        var selected = $('#table-grid').yiiGridView('getSelectedRows')
        if(selected.length != 2) alert('Please select two versions for comparison.')
        else {
            parent.open($(this).data('url')+'?id1='+selected[0]+'&id2='+selected[1],'',"width=1000,height=600,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no")//.focus()
        }
    })
    $('#keep-button').on('click', function(e){
        var selected = $('#table-grid').yiiGridView('getSelectedRows')
        if(selected.length < 1) alert('Please select versions.')
        else {
            $.post($(this).data('url'), {id:selected}, function(response){
                window.location.href = window.location.href;
            })
        }
    })
     $('#delete-button').on('click', function(e){
        var selected = $('#table-grid').yiiGridView('getSelectedRows')
        if(selected.length < 1) alert('Please select versions.')
        else {
            $.post($(this).data('url'), {id:selected}, function(response){
                window.location.href = window.location.href;
            })
        }
    })
    /*$('#table-grid').on('click', 'a.btn-restore', function(e){
        e.preventDefault()
        $.post($(this).attr('href'), function(response){
            parent.location.href = parent.location.href;
        })
    })
    /*$('#table-grid').on('click', 'a.btn-keep-forever, .btn-delete', function(e){
        e.preventDefault()
        $.post($(this).attr('href'), function(response){
            window.location.href = window.location.href;
        })
    })*/
    $('#table-grid').on('click', 'a.btn-preview', function(e){
        e.preventDefault()
        parent.open($(this).attr('href'),'',"width=1000,height=600,resizable=yes,scrollbars=yes,toolbar=no,location=no,directories=no,status=no,menubar=no")//.focus()
    })

    $('[data-toggle="tooltip"]').tooltip({container: 'body'})
}(jQuery)
JS
    ) ?>

    <br/>
    <?php $itemVersionHash = $item->version ? $item->version->version_hash : null ?>

	<?= GridView::widget([
        'id'=>'table-grid',
		'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-striped'],
        'options' => ['class' => 'clearfix'],
		'columns' => [
            ['class' => 'yii\grid\CheckboxColumn'],
            [
                'attribute' => 'created_at',
                'value' => function($model) use($itemVersionHash) {
                        /** $model \gromver\platform\basic\modules\version\models\Version */
                        return date('d.m.Y H:i', $model->created_at).($itemVersionHash == $model->version_hash ? ' <small class="glyphicon glyphicon-star"></small>' : '');
                    },
                'format' => 'raw'
            ],
			'version_note',
            [
                'attribute' => 'keep_forever',
                'value' => function($model) {
                        /** $model \gromver\platform\basic\modules\version\models\Version */
                        return Html::a($model->keep_forever ? Yii::t('gromver.platform', 'Yes') . ' <small class="glyphicon glyphicon-lock"></small>' : Yii::t('gromver.platform', 'No'), ['keep-forever', 'id' => $model->id], ['class' => 'btn btn-xs btn-default active btn-keep-forever', 'data-method' => 'post']);
                    },
                'format' => 'raw'
            ],
			[
                'attribute' => 'created_by',
                'value' => function($model) {
                        /** $model \gromver\platform\basic\modules\version\models\Version */
                        return $model->user->username;
                    }
            ],
            'character_count',
			[
                'class' => 'yii\grid\ActionColumn',
                'buttons' => [
                    'restore' => function($url, $model) {
                            /** $model \gromver\platform\basic\modules\version\models\Version */
                            return Html::a('<i class="glyphicon glyphicon-open"></i>', ['restore', 'id' => $model->id, 'modal' => 1], [
                                'title' => Yii::t('gromver.platform', 'Restore'),
                                'data-method' => 'post',
                                'class' => 'btn-restore'
                            ]);
                        },
                    'preview' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-eye-open"></i>', $url, [
                                'title' => Yii::t('gromver.platform', 'Preview'),
                                'class' => 'btn-preview'
                            ]);
                        },
                    'delete' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-trash"></i>', $url, [
                                'title' => Yii::t('gromver.platform', 'Delete'),
                                'data-confirm' => Yii::t('gromver.platform', 'Are you sure to delete this item?'),
                                'data-method' => 'delete',
                                'class' => 'btn-delete'
                            ]);
                        },
                    'update' => function ($url, $model) {
                            return Html::a('<i class="glyphicon glyphicon-pencil"></i>', ['update', 'id' => $model->id, 'modal' => 1], [
                                'title' => Yii::t('gromver.platform', 'Update'),
                            ]);
                        }
                ],
                'template'=>'{restore} {preview} {update} {delete}'
            ],
		],
	]); ?>

</div>