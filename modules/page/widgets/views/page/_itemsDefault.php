<?php
/**
 * @var $this yii\web\View
 * @var $items []
 * @var $level integer
 * @var $collapse bool
 * @var $id
 */

use yii\helpers\Html; ?>

<ul id="<?=$id?>" class="level-<?= $level ?> collapse<?= ($collapse ? '' : ' in') ?> list-unstyled">
    <?php foreach($items as $item) {
        $ulId = $this->context->id . '-u' . $item['id'];
        if (isset($item['items'])) {
            $options = [
                'class' => $item['hasActiveChild'] ? 'hasActiveChild' : null
            ];
            if ($item['active']) {
                Html::addCssClass($options, 'active');
            }
            echo Html::beginTag('li', $options);

            echo Html::a($item['label'] . '<i class="glyphicon glyphicon-chevron-down"></i><i class="glyphicon glyphicon-chevron-up"></i>', null, [
                'class' => 'toggle' . ($item['hasActiveChild'] || $item['active'] ? '' : ' collapsed'),
                'data' => [
                    'toggle' => 'collapse',
                    'target' => '#' . $ulId
                ],
            ]);

            echo $this->render('_itemsDefault', [
                'items' => $item['items'],
                'level' => $level + 1,
                'collapse' => !($item['hasActiveChild'] || $item['active']),
                'id' => $ulId
            ]);

            echo Html::endTag('li');
        } else {
            echo Html::beginTag('li', [
                'class' => $item['active'] ? 'active' : null,
            ]);

            echo $item['active'] ? Html::tag('span', $item['label']) : Html::a($item['label'], $item['url']);

            echo Html::endTag('li');
        }
    } ?>
</ul>
