<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\components;


/**
 * Class User
 * @property \gromver\platform\basic\modules\user\models\User $identity The identity object associated with the currently logged user. Null
 * is returned if the user is not logged in (not authenticated).
 *
 * @package yii2-platform-basic
 * @author Ricardo Obregón <robregonm@gmail.com>
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class User extends \yii\web\User
{
    /**
	 * @inheritdoc
	 */
	public $identityClass = 'gromver\platform\basic\modules\user\models\User';

	/**
	 * @inheritdoc
	 */
	public $enableAutoLogin = true;

	/**
	 * @inheritdoc
	 */
	public $loginUrl = ['/grom/auth/default/login'];

    public $superAdmins = ['admin'];

    public $defaultRoles = ['Authorized'];

	/**
	 * @inheritdoc
	 */
	protected function afterLogin($identity, $cookieBased, $duration)
	{
		parent::afterLogin($identity, $cookieBased, $duration);
		$this->identity->setScenario(self::EVENT_AFTER_LOGIN);
		$this->identity->setAttribute('last_visit_at', time());
		//todo сэйвить али не сэйвить ип юзера?
		// $this->identity->setAttribute('login_ip', ip2long(\Yii::$app->getRequest()->getUserIP()));
		$this->identity->save(false);
	}

	public function getIsSuperAdmin()
	{
		if ($this->isGuest) {
			return false;
		}
		return $this->identity->getIsSuperAdmin();
	}

    /**
     * @inheritdoc
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        // Always return true when SuperAdmin user
        if ($this->getIsSuperAdmin()) {
            return true;
        }
        return parent::can($permissionName, $params, $allowCaching);
    }
}