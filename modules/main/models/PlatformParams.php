<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\main\models;


use Yii;

/**
 * Class CmsParams
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class PlatformParams extends  \yii\base\Object
{
    /**
     * @translation gromver.platform
     */
    public $siteName;
    /**
     * @type multiple
     * @fieldType text
     * @email
     * @translation gromver.platform
     */
    public $adminEmail;
    /**
     * @type multiple
     * @fieldType text
     * @email
     * @translation gromver.platform
     */
    public $supportEmail;
    /**
     * @before <h3 class="col-sm-12">Metadata</h3>
     * @translation gromver.platform
     * @label Meta description
     */
    public $description;
    /**
     * @translation gromver.platform
     * @label Meta keywords
     */
    public $keywords;
    /**
     * @type list
     * @items robots
     * @translation gromver.platform
     * @label Robots
     */
    public $robots;

    public static function robots()
    {
        return [
            '' => Yii::t('gromver.platform', 'Empty'),
            'index, follow' => 'Index, Follow',
            'noindex, follow' => 'No index, follow',
            'index, nofollow' => 'Index, No follow',
            'noindex, nofollow' => 'No index, no follow'
        ];
    }
}