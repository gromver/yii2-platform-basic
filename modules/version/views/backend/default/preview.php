<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/**
 * @var yii\web\View $this
 * @var gromver\platform\basic\version\models\Version $model
 */

$this->title = $model->item_class . ' ID: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-view">

	<h1><?= Html::encode($this->title) ?></h1>

	<?= DetailView::widget([
		'model' => $model,
        'options'=>[
            'class'=>'table table-striped detail-view',
            'style'=>'word-break: break-all'
        ],
        'template'=>'<tr><th style="width:20%">{label}</th><td>{value}</td></tr>',
		/*'attributes' => [
			'id',
			'item_id',
			'item_class',
			'version_note',
			'created_at',
			'created_by',
			'character_count',
			'version_hash',
			'version_data:ntext',
			'keep_forever',
		],*/
	]) ?>

</div>
