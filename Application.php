<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic;

use yii\helpers\ArrayHelper;

/**
 * Class Application
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class Application extends \yii\web\Application {
    public $language = 'en';
    public $languages = ['en', 'ru'];
    public $sourceLanguage = 'en';
    public $defaultRoute = 'grom/frontend/default/index';
    public $layout = '@gromver/platform/basic/views/layouts/main';

    private $_modulesHash;
    
    /**
     * @inheritdoc
     */
    public function __construct($config = [])
    {
        $config = ArrayHelper::merge([
            'components' => [
                'request' => [
                    'class' => 'gromver\platform\basic\components\Request',
                ],
                'urlManager' => [
                    'class' => 'gromver\platform\basic\components\UrlManager',
                    'enablePrettyUrl' => true,
                    'showScriptName' => false,
                ],
                'user' => [
                    'class' => 'gromver\platform\basic\components\User',
                ],
                'errorHandler' => [
                    'class' => 'yii\web\ErrorHandler',
                    'errorAction' => '/grom/common/default/error'
                ],
                'authManager' => [
                    'class' => 'yii\rbac\DbManager',
                    'itemTable' => '{{%grom_auth_item}}',
                    'itemChildTable' => '{{%grom_auth_item_child}}',
                    'assignmentTable' => '{{%grom_auth_assignment}}',
                    'ruleTable' => '{{%grom_auth_rule}}'
                ],
                'cache' => ['class' => 'yii\caching\FileCache'],
                'elasticsearch' => ['class' => 'yii\elasticsearch\Connection'],
                'assetManager' => [
                    'bundles' => [
                        'mihaildev\ckeditor\Assets' => [
                            'sourcePath' => '@gromver/platform/basic/assets/ckeditor',
                        ],
                    ],
                ],
                'i18n' => [
                    'translations' => [
                        '*' => [
                            'class' => 'yii\i18n\PhpMessageSource'
                        ],
                    ],
                ],
            ],
            'modules' => [
                'grom' => [
                    'class' => 'gromver\platform\basic\modules\main\Module',
                    'modules' => [
                        'user'      => ['class' => 'gromver\platform\basic\modules\user\Module'],
                        'auth'      => ['class' => 'gromver\platform\basic\modules\auth\Module'],
                        'menu'      => ['class' => 'gromver\platform\basic\modules\menu\Module'],
                        'news'      => ['class' => 'gromver\platform\basic\modules\news\Module'],
                        'page'      => ['class' => 'gromver\platform\basic\modules\page\Module'],
                        'tag'       => ['class' => 'gromver\platform\basic\modules\tag\Module'],
                        'version'   => ['class' => 'gromver\platform\basic\modules\version\Module'],
                        'widget'    => ['class' => 'gromver\platform\basic\modules\widget\Module'],
                        'media'     => ['class' => 'gromver\platform\basic\modules\media\Module'],
                        //'search'    => ['class' => 'gromver\platform\basic\modules\elasticsearch\Module'],
                    ]
                ],
                'gridview' => ['class' => 'kartik\grid\Module']
            ]
        ], $config);

        $this->_modulesHash = md5(json_encode(ArrayHelper::getValue($config, 'modules', [])));

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->bootstrap = array_merge($this->bootstrap, ['grom']);

        parent::init();
    }


    /**
     * @return string
     */
    public function getModulesHash() {
        return $this->_modulesHash;
    }

    /**
     * @return array
     */
    public function getLanguagesList()
    {
        return array_combine($this->languages, $this->languages);
    }

    /**
     * @return \yii\elasticsearch\Connection
     */
    public function getElasticSearch()
    {
        return $this->get('elasticsearch');
    }
}