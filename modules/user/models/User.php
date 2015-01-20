<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\modules\user\models;


use Yii;
use yii\base\ModelEvent;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\console\Application;
use yii\helpers\Json;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "grom_user".
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 *
 * @property integer $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $auth_key
 * @property string $params
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $deleted_at
 * @property integer $last_visit_at
 *
 * @property \gromver\platform\basic\modules\news\models\Post[] $viewedPosts
 * @property string[] $roles
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 1;
    const STATUS_ACTIVE = 2;
    const STATUS_SUSPENDED = 3;

    const EVENT_BEFORE_SAFE_DELETE = 'beforeSafeDelete';
    const EVENT_AFTER_SAFE_DELETE = 'afterSafeDelete';

    /**
     * @var string the raw password. Used to collect password input and isn't saved in database
     */
    public $password;
    /**
     * @var string the raw password confirmation. Used to check password input and isn't saved in database
     */
    public $password_confirm;
    /**
     * @var string код капчи введеный пользователем
     */
    public $verifyCode;

    private static $_statuses = [
        self::STATUS_DELETED => 'Deleted',
        self::STATUS_INACTIVE => 'Inactive',
        self::STATUS_ACTIVE => 'Active',
        self::STATUS_SUSPENDED => 'Suspended',
    ];

    private $_isSuperAdmin = null;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%grom_user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'password_hash', 'created_at'], 'required'],
            [['status', 'created_at', 'updated_at', 'deleted_at', 'last_visit_at'], 'integer'],
            [['username'], 'string', 'max' => 64],
            [['email', 'password_hash', 'auth_key'], 'string', 'max' => 128],
            [['password_reset_token'], 'string', 'max' => 32],

            ['status', 'default', 'value' => static::STATUS_ACTIVE, 'on' => ['signup', 'signupWithCaptcha']],
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'string', 'max' => 64],
            ['username', 'required'],
            ['username', 'unique', 'message' => Yii::t('gromver.platform', 'This username has already been taken.'), 'on' => ['create', 'signup', 'signupWithCaptcha']],
            ['username', 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'filter' => function ($query) {
                    /** @var $query \yii\db\ActiveQuery */
                    $query->andWhere('status!='.self::STATUS_DELETED);
                }, 'message' => Yii::t('gromver.platform', 'This email address has already been taken.'), 'on' => ['create', 'signup', 'signupWithCaptcha']],
            ['email', 'unique', 'filter' => function ($query) {
                    /** @var $query \yii\db\ActiveQuery */
                    $query->andWhere('status!='.self::STATUS_DELETED);
                },
                'when' => function () {
                        /** @var $query static */
                        return $this->status != self::STATUS_DELETED;
                    },
                'message' => Yii::t('gromver.platform', 'This email address has already been taken.'), 'on' => 'default'],
            ['email', 'exist', 'message' => Yii::t('gromver.platform', 'There is no user with such email.'), 'on' => 'requestPasswordResetToken'],

            ['password', 'required', 'on' => ['create', 'signup', 'resetPassword', 'signupWithCaptcha']],
            ['password', 'string', 'min' => 6],

            ['password_confirm', 'compare', 'compareAttribute' => 'password', 'skipOnEmpty' => false, 'on' => ['create', 'resetPassword', 'update', 'profile']],
            ['verifyCode', 'captcha', 'captchaAction' => 'grom/auth/default/captcha', 'on' => 'signupWithCaptcha']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('gromver.platform', 'ID'),
            'username' => Yii::t('gromver.platform', 'Username'),
            'email' => Yii::t('gromver.platform', 'Email'),
            'password' => Yii::t('gromver.platform', 'Password'),
            'password_hash' => Yii::t('gromver.platform', 'Password Hash'),
            'password_reset_token' => Yii::t('gromver.platform', 'Password Reset Token'),
            'password_new' => Yii::t('gromver.platform', 'New Password'),
            'password_confirm' => Yii::t('gromver.platform', 'Confirm Password'),
            'auth_key' => Yii::t('gromver.platform', 'Auth Key'),
            'params' => Yii::t('gromver.platform', 'Params'),
            'status' => Yii::t('gromver.platform', 'Status'),
            'roles' => Yii::t('gromver.platform', 'Roles'),
            'created_at' => Yii::t('gromver.platform', 'Created At'),
            'updated_at' => Yii::t('gromver.platform', 'Updated At'),
            'deleted_at' => Yii::t('gromver.platform', 'Deleted At'),
            'last_visit_at' => Yii::t('gromver.platform', 'Last Visit At'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return [
            'signup' => ['username', 'email', 'password'],
            'create' => ['username', 'email', 'password', 'password_confirm', 'status', 'roles'],
            'signupWithCaptcha' => ['username', 'email', 'password','verifyCode'],
            'profile' => ['username', 'email', 'password', 'password_confirm'],
            'resetPassword' => ['password', 'password_confirm'],
            'requestPasswordResetToken' => ['email'],
            'login' => ['last_visit_time'],
            'update' => ['status', 'roles', 'password', 'password_confirm'],
        ] + parent::scenarios();
    }

    /**
     * todo
     * @return \yii\db\ActiveQuery
     */
    public function getViewedPosts()
    {
        //return $this->hasMany(CmsPostViewed::className(), ['user_id' => 'id']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    self::EVENT_BEFORE_UPDATE => 'updated_at',
                    self::EVENT_BEFORE_SAFE_DELETE => 'deleted_at',
                ]
            ]
        ];
    }

    public function getStatusLabel($status = null)
    {
        if ($status === null) {
            return Yii::t('gromver.platform', self::$_statuses[$this->status]);
        }
        return Yii::t('gromver.platform', self::$_statuses[$status]);
    }

    public static function statusLabels()
    {
        return array_map(function ($label) {
                return Yii::t('gromver.platform', $label);
            }, self::$_statuses);
    }

    /**
     * Finds an identity by the given ID.
     *
     * @param string|integer $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return null|User
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => static::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('"findIdentityByAccessToken" is not implemented.');
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        $parts = explode('_', $token);
        $timestamp = (int)end($parts);
        if ($timestamp + $expire < time()) {
            // token expired
            return null;
        }

        return static::findOne([
                'password_reset_token' => $token,
                'status' => self::STATUS_ACTIVE,
            ]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }


    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {
            if (($this->isNewRecord || in_array($this->getScenario(), ['resetPassword', 'profile', 'update'])) && !empty($this->password)) {
                $this->password_hash = Yii::$app->security->generatePasswordHash($this->password);
                $this->password_reset_token = null;
            }
            if ($this->isNewRecord) {
                $this->auth_key = Yii::$app->security->generateRandomString();
            }

            return true;
        }
        return false;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        $newRoles = $this->_roles;
        if(is_array($newRoles))
            $this->_roles = null;
        else
            return;

        $oldRoles = $this->getRoles();

        $toAssign = array_diff($newRoles, $oldRoles);
        $toRevoke = array_diff($oldRoles, $newRoles);

        $auth = Yii::$app->authManager;

        foreach($toAssign as $role)
            $auth->assign($auth->getRole($role), $this->id);

        foreach($toRevoke as $role)
            $auth->revoke($auth->getRole($role), $this->id);
    }

    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            return Yii::$app instanceof Application ? true : !$this->getIsSuperAdmin();
        } else {
            return false;
        }
    }

    public function safeDelete()
    {
        if ($this->status !== self::STATUS_DELETED) {
            $this->status = self::STATUS_DELETED;
            if ($this->beforeSafeDelete() && $this->save(false)) {
                $this->afterSafeDelete();
            } else {
                return false;
            }
        }

        return true;
    }

    public function beforeSafeDelete()
    {
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_SAFE_DELETE, $event);

        return $event->isValid;
    }

    public function afterSafeDelete()
    {
        $this->trigger(self::EVENT_AFTER_SAFE_DELETE);
    }

    /**
     * Returns whether the logged in user is an administrator.
     *
     * @return boolean the result.
     */
    public function getIsSuperAdmin()
    {
        if ($this->_isSuperAdmin !== null) {
            return $this->_isSuperAdmin;
        }

        $this->_isSuperAdmin = in_array($this->username, Yii::$app->user->superAdmins);
        return $this->_isSuperAdmin;
    }

    public function login($duration = 0)
    {
        return Yii::$app->user->login($this, $duration);
    }

    private $_roles;

    public function setRoles($value)
    {
        $roles = array_keys(Yii::$app->authManager->getRoles());
        $this->_roles = array_intersect((array)$value, $roles);
    }

    public function getRoles()
    {
        if(!isset($this->_roles))
            $this->_roles = array_keys(Yii::$app->authManager->getRolesByUser($this->id));

        return $this->_roles;
    }

    public function getParamsArray()
    {
        return Json::decode($this->params);
    }

    public function setParamsArray($value)
    {
        $this->params = Json::encode($value);
    }

    /**
     * @param $permissionName
     * @param array $params
     * @return bool
     */
    public function can($permissionName, $params = [])
    {
        return $this->getIsSuperAdmin() ? $this->getIsSuperAdmin() : Yii::$app->authManager->checkAccess($this->id, $permissionName, $params);
    }
}
