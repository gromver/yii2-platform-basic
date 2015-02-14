<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\widgets;


use gromver\platform\basic\modules\news\models\Post;
use Yii;

/**
 * Class Calendar
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property string $day
 * @property string $month
 * @property string $year
 * @property integer $currentMonthDate
 * @property integer $previousMonthDate
 * @property integer $nextMonthDate
 * @property integer $daysInMonth
 * @property integer $firstDayOfTheWeek
 * @property array $previousYearLink
 * @property array $nextYearLink
 * @property array $previousMonthLink
 * @property array $nextMonthLink
 */
class Calendar extends Widget
{
    private $_year;
    private $_month;
    private $_day;

    public $categoryId;
    public $route = '/grom/news/frontend/post/day';

    private $_calendar = [];

    /**
     * @type list
     * @items layouts
     */
    public $layout = 'calendar/default';

    public function init()
    {
        $query = Post::find()
            ->published()
            ->category($this->categoryId)
            ->andWhere('published_at>=:begin AND published_at<:end', [':begin' => $this->getCurrentMonthDate(), ':end' => $this->getNextMonthDate()])
            ->groupBy('day')
            ->select(['count' => 'count(id)', 'day' => 'day(from_unixtime(published_at))'])
            ->last()
            ->asArray();

        foreach($query->all() as $item) {
            $this->_calendar[$item['day']] = $item['count'];
        }
    }

    public function run()
    {
        echo $this->render($this->layout, [
            'widget' => $this
        ]);
    }

    public function setYear($value)
    {
        $this->_year = intval($value);
    }

    public function getYear()
    {
        return $this->_year ? $this->_year : date('Y');
    }

    public function setMonth($value)
    {
        if ($value>=1 && $value<=12) {
            $this->_month = intval($value);
        }
    }

    public function getMonth()
    {
        return $this->_month ? $this->_month : date('n');
    }

    public function setDay($value)
    {
        if (isset($this->_year) && isset($this->_month) && $this->dayIsInCurrentMonth(intval($value))) {
            $this->_day = intval($value);
        }
    }

    //возвращает текущий день если просматривается текущий месяц
    public function getDay()
    {
        return $this->_day;
    }

    public function getCurrentMonthDate()
    {
        return mktime(0,0,0,$this->month,1,$this->year);
    }

    public function getPreviousMonthDate()
    {
        return $this->month==1 ? mktime(0,0,0,12,1,$this->year-1) : mktime(0,0,0,$this->month-1,1,$this->year);
    }

    public function getNextMonthDate()
    {
        return $this->month==12 ? mktime(0,0,0,1,1,$this->year+1) : mktime(0,0,0,$this->month+1,1,$this->year);
    }

    public function getDaysInCurrentMonth() {
        return $this->getDaysInMonth($this->month, $this->year);
    }

    public function getDaysInMonth($month, $year) {
        return cal_days_in_month(CAL_GREGORIAN, $month, $year);
    }

    private function dayIsInCurrentMonth($day) {
        return $day >= 1 && $day <= $this->daysInCurrentMonth;
    }

    public function getFirstDayOfTheWeek() {
        $w = date('w', mktime(0,0,0,$this->month,1,$this->year));
        return $w==0?6:--$w;
    }

    public function getPreviousYearLink() {
        return [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year-1, 'month'=>$this->month, 'day'=>0];
    }
    public function getNextYearLink() {
        return [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year+1, 'month'=>$this->month, 'day'=>0];
    }

    public function getPreviousMonthLink() {
        return $this->month==1 ? [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year-1, 'month'=>12, 'day'=>0] : [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year, 'month'=>$this->month-1, 'day'=>0];
    }
    public function getNextMonthLink() {
        return $this->month==12 ? [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year+1, 'month'=>1, 'day'=>0] : [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year, 'month'=>$this->month+1, 'day'=>0];
    }

    public function getDayLink($day)
    {
        if (isset($this->_calendar[$day])) {
            return [$this->route, 'category_id'=>$this->categoryId, 'year'=>$this->year, 'month'=>$this->month, 'day'=>$day];
        }
    }

    public function getDayPostsCount($day)
    {
        return @$this->_calendar[$day];
    }

    public static function layouts()
    {
        return [
            'calendar/default' => 'Default',
        ];
    }
}