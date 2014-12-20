<?php
/**
 * @var yii\web\View $this
 * @var gromver\platform\basic\modules\user\models\User $user
 */

echo Yii::t('gromver.platform', 'For change of the password follow the <a href="{link}">link</a>', ['link' => \yii\helpers\Url::toRoute(['/grom/auth/default/reset-password', 'token' => $user->password_reset_token], true)]);