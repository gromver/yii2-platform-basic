<?php
/**
 * @var $this yii\web\View
 * @var $widget \gromver\platform\basic\modules\news\widgets\Calendar
 */

use yii\helpers\Html;
?>
<?php \yii\widgets\Pjax::begin([
    'id' => 'news-calendar',
    'enablePushState' => false
]) ?>

<div id="<?= $widget->id ?>" class="calendar">
    <div class="calendar-container">
        <div class="month-year-nav clearfix navbar-inverse navbar">
            <div class="navbar-text pull-left">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i>', $widget->previousMonthLink, ['class' => 'navbar-link']) . Html::tag('span', Yii::$app->formatter->asDate($widget->currentMonthDate, 'MMM')) . Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', $widget->nextMonthLink, ['class' => 'navbar-link']) ?>
            </div>
            <div class="navbar-text pull-right">
                <?= Html::a('<i class="glyphicon glyphicon-chevron-left"></i>', $widget->previousYearLink, ['class' => 'navbar-link']) . Html::tag('span', $widget->getYear()) . Html::a('<i class="glyphicon glyphicon-chevron-right"></i>', $widget->nextYearLink, ['class' => 'navbar-link']) ?>
            </div>
        </div>
    </div>
    <table>
        <thead>
        <tr class="weekdays-row">
            <?php for($i=3;$i<10;$i++) : ?>
                <th><?= Yii::$app->formatter->asDate(mktime(0,0,0,1,$i,2000), 'E') ?></th>
            <?php endfor; ?>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php $daysStarted = false; $day = 1; ?>
            <?php for($i = 1; $i <= $widget->daysInCurrentMonth+$widget->firstDayOfTheWeek; $i++): ?>
            <?php if(!$daysStarted) $daysStarted = ($i == $widget->firstDayOfTheWeek+1); ?>
            <td <?php if($daysStarted && $day == $widget->day) echo 'class="selected-day"'; ?>>
                <?php if($daysStarted && $day <= $widget->daysInCurrentMonth) {
                    if ($count = $widget->getDayPostsCount($day)) {
                        echo Html::a($day, $widget->getDayLink($day), ['title' => Yii::t('gromver.platform', 'There {0, plural, =0{are no posts} =1{is one post} other{are # posts}}', $count), 'data-pjax' => 0]);
                    } else {
                        echo $day;
                    }
                    $day++;
                } ?>
            </td>
            <?php if($i % 7 == 0): ?>
        </tr><tr>
            <?php endif; ?>
            <?php endfor; ?>
        </tr>
        </tbody>
    </table>
</div>

<?php \yii\widgets\Pjax::end() ?>

<style>
    .calendar .month-year-nav {
        margin: 0 -10px;
        font-size: 12px;
        text-align: center;
        border-radius: 0;
    }
    .calendar .month-year-nav span {
        margin: 0 8px;
    }
    .calendar {
        padding: 0 10px;
        background-color: #F7F7F7;
        overflow: hidden;
    }
    .calendar table {
        width: 100%;
        margin: 0 0 1em 0;
    }
    .calendar table td, .calendar table th {
        text-align: center;
        vertical-align: middle;
        width: 14%;
        padding: 8px 0;
        font-size: 12px;
        color: #A29E9E;
    }
    .calendar table th {
        color: #444444;
    }
    .calendar table a {
        color: #222222;
        font-weight: bold;
        text-decoration: none;
    }
    .calendar table a:hover {
        text-decoration: underline;
    }
    .calendar .selected-day {
        background-color: #222222;
        color: #777777;
    }
    .calendar .selected-day a {
        color: #777777;
    }
    .arrow-left, .arrow-right {
        border-right: 6px solid #FFF;
        display: inline-block;
        width: 0;
        margin: 0 8px 0 0;
        border-top: 5px solid transparent;
        border-bottom: 5px solid transparent;
        font: 0/0 serif;
        line-height: 0;
    }
    .arrow-right {
        border-left: 6px solid #FFF;
        border-right: none;
        margin: 0 0 0 8px;
    }
</style>