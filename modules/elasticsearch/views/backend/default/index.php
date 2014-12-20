<?php

/* @var $this yii\web\View */
/* @var $query string */

$this->title = Yii::t('gromver.platform', 'Search');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="search-default-index">
    <h1><?= Yii::t('gromver.platform', 'Search') ?></h1>

    <?php echo \gromver\platform\basic\widgets\SearchForm::widget([
        'id' => 'bSearchForm',
        'query' => $query,
        'showPanel' => false
    ]);

    echo \gromver\platform\basic\widgets\SearchResults::widget([
        'id' => 'bSearchResult',
        'query' => $query,
        'filters' => [],
    ]); ?>
</div>
