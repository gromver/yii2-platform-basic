<?php

use gromver\platform\basic\modules\search\modules\elastic\models\Index;
use yii\console\Exception;
use yii\elasticsearch\ActiveRecord;

class m000010_000000_grom_elastic_search_create_tables extends \yii\db\Migration
{
    public function up()
    {
        $connection = ActiveRecord::getDb();

        if (!$index = Index::index()) {
            throw new Exception(Index::className() . '::index must be set.');
        }

        $type = Index::type();

        if ($connection->createCommand()->indexExists($index)) {
            $connection->createCommand()->deleteIndex($index);
        }

        $connection->createCommand()->createIndex($index, [
            /*'settings' => [
                'analysis' => [
                    'analyzer' => [
                        'grom_analyzer' => [
                            'type' => 'custom',
                            'char_filter' => 'html_strip',
                            'tokenizer' => 'standard',
                            'filter' => ["lowercase", "russian_morphology", "english_morphology", "grom_stopwords"]
                        ]
                    ],
                    'filter' => [
                        'grom_stopwords' => [
                            'type' => 'stop',
                            'stopwords' => 'а,без,более,бы,был,была,были,было,быть,в,вам,вас,весь,во,вот,все,всего,всех,вы,где,да,даже,для,до,его,ее,если,есть,еще,же,за,здесь,и,из,или,им,их,к,как,ко,когда,кто,ли,либо,мне,может,мы,на,надо,наш,не,него,нее,нет,ни,них,но,ну,о,об,однако,он,она,они,оно,от,очень,по,под,при,с,со,так,также,такой,там,те,тем,то,того,тоже,той,только,том,ты,у,уже,хотя,чего,чей,чем,что,чтобы,чье,чья,эта,эти,это,я,a,an,and,are,as,at,be,but,by,for,if,in,into,is,it,no,not,of,on,or,such,that,the,their,then,there,these,they,this,to,was,will,with'
                        ]
                    ]
                ]
            ],*/
            'mappings' => [
                $type => [
                    //'_all' => ['analyzer' => 'grom_analyzer'],
                    'properties' => [
                        'model_class' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                        'model_id' => [
                            'type' => 'integer',
                            'include_in_all' => false
                        ],
                        'title' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer'
                        ],
                        'content' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer',
                            //"index_options" => "offsets",
                            //"term_vector" => "with_positions_offsets"
                        ],
                        'tags' => [
                            'type' => 'string',
                        ],
                        'updated_at' => [
                            "type" => "date"
                        ],
                        'url_frontend' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                        'url_backend' => [
                            'type' => 'string',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                    ]
                ],
            ]
        ]);

        echo "Index \"$index\" created.\n";
    }

    public function down()
    {
        if (!$index = Index::index()) {
            throw new Exception(Index::className() . '::index must be set.');
        }

        ActiveRecord::getDb()->createCommand()->deleteIndex($index);//->deleteAllIndexes();//

        echo "Index $index are deleted successfully.";
    }

}