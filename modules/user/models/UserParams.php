<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\models;

/**
 * Class UserParams
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class UserParams {
    /**
     * @translation gromver.platform
     */
    public $name;
    /**
     * @translation gromver.platform
     */
    public $surname;
    /**
     * @translation gromver.platform
     */
    public $patronymic;
    /**
     * @translation gromver.platform
     */
    public $phone;
    /**
     * @translation gromver.platform
     */
    public $work_phone;
    /**
     * @type text
     * @email
     * @translation gromver.platform
     * @var string
     */
    public $email;
    /**
     * @type text
     * @email
     * @translation gromver.platform
     * @var string
     */
    public $work_email;
    /**
     * @translation gromver.platform
     */
    public $address;
} 