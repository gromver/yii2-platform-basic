<?php
/**
 * @link https://github.com/gromver/yii2-platform-basic.git#readme
 * @copyright Copyright (c) Gayazov Roman, 2014
 * @license https://github.com/gromver/yii2-platform-basic/blob/master/LICENSE
 * @package yii2-platform-basic
 * @version 1.0.0
 */

namespace gromver\platform\basic\console\components;

/**
 * Class ModuleMigrateController
 * @package yii2-platform-basic
 * @author Gayazov Roman <gromver5@gmail.com>
 */
class ModuleMigrateController extends \bariew\moduleMigration\ModuleMigrateController {
    /**
     * creates $allMigrationPaths attribute from module base paths
     * @param \yii\base\Module|null $module
     */
    protected function attachModuleMigrations($module = null)
    {
        $module or $module = \Yii::$app;
        foreach ($module->modules as $name => $config) {
            $basePath = $module->getModule($name)->basePath;
            $path = $basePath . DIRECTORY_SEPARATOR. 'migrations';
            if (file_exists($path) && !is_file($path)) {
                $this->allMigrationPaths[$name] = $path;
            }
            $this->attachModuleMigrations($module->getModule($name));
        }
    }
} 