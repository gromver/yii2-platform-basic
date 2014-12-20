<?php

use yii\db\Migration;

class m140820_085041_grom_site_setup extends Migration
{
    public function up()
    {
        // Creates the default platform config
        /** @var \gromver\platform\basic\console\modules\main\Module $main */
        $cmf = Yii::$app->grom;
        $paramsPath = Yii::getAlias($cmf->paramsPath);
        $paramsFile = $paramsPath . DIRECTORY_SEPARATOR . 'params.php';

        $params = $cmf->params;

        $model = new \gromver\models\ObjectModel(\gromver\platform\basic\common\models\PlatformParams::className());
        $model->setAttributes($params);

        echo 'Setup application config: ' . PHP_EOL;
        $this->readStdinUser('Site Name (My Site)', $model, 'siteName', 'My Site');
        $this->readStdinUser('Admin Email (admin@email.com)', $model, 'adminEmail', 'admin@email.com');
        $this->readStdinUser('Support Email (support@email.com)', $model, 'supportEmail', 'support@email.com');
        $this->readStdinUser('Elasticsearch Index', $model, 'elasticsearchIndex');

        if ($model->validate()) {

            \yii\helpers\FileHelper::createDirectory($paramsPath);
            file_put_contents($paramsFile, '<?php return ' . var_export($model->toArray(), true) . ';');
            @chmod($paramsFile, 0777);
        }

        echo 'Setup complete.' . PHP_EOL;
    }

    /*public function down()
    {
        echo "m141128_060147_cmf_site_setup cannot be reverted.\n";

        return false;
    }*/

    /**
     * @param string $prompt
     * @param \yii\base\Model $model
     * @param string $field
     * @param string $default
     * @return string
     */
    private function readStdinUser($prompt, $model, $field, $default = '')
    {
        while (!isset($input) || !$model->validate(array($field))) {
            echo $prompt . (($default) ? " [$default]" : '') . ': ';
            $input = (trim(fgets(STDIN)));
            if (empty($input) && !empty($default)) {
                $input = $default;
            }
            $model->$field = $input;
        }
        return $input;
    }
}
