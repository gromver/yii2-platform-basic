<?php

use yii\db\Migration;
use yii\helpers\Json;

class m140825_160749_grom_mapping extends Migration
{
    public function up()
    {
        $connection = \yii\elasticsearch\ActiveRecord::getDb();

        $index = \gromver\platform\basic\common\models\elasticsearch\ActiveDocument::index();

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
                'page' => [
                    //'_all' => ['analyzer' => 'grom_analyzer'],
                    'properties' => [
                        'model_class' => [
                            'type' => 'string',
                            'index' => 'no',
                        ],
                        'published' => [
                            'type' => 'boolean',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                        'language' => [
                            'type' => 'string',
                            'include_in_all' => false
                        ],
                        'title' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer'
                        ],
                        'text' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer',
                            //"index_options" => "offsets",
                            //"term_vector" => "with_positions_offsets"
                        ],
                        'metakey' => [
                            'type' => 'string'
                        ],
                        'metadesc' => [
                            'type' => 'string'
                        ],
                        'tags' => [
                            'type' => 'string',
                            'index_name' => 'tag'
                        ],
                        'date' => [
                            "type" => "date"
                        ]
                    ]
                ],
                'post' => [
                    //'_all' => ['analyzer' => 'grom_analyzer'],
                    'properties' => [
                        'model_class' => [
                            'type' => 'string',
                            'index' => 'no',
                        ],
                        'published' => [
                            'type' => 'boolean',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                        'language' => [
                            'type' => 'string',
                            'include_in_all' => false
                        ],
                        'category_id' => [
                            'type' => 'integer',
                            'include_in_all' => false
                        ],
                        'title' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer'
                        ],
                        'text' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer',
                            //"index_options" => "offsets",
                            //"term_vector" => "with_positions_offsets"
                        ],
                        'metakey' => [
                            'type' => 'string'
                        ],
                        'metadesc' => [
                            'type' => 'string'
                        ],
                        'tags' => [
                            'type' => 'string',
                            'index_name' => 'tag'
                        ],
                        'date' => [
                            "type" => "date"
                        ]
                    ]
                ],
                'category' => [
                    //'_all' => ['analyzer' => 'grom_analyzer'],
                    'properties' => [
                        'model_class' => [
                            'type' => 'string',
                            'index' => 'no',
                        ],
                        'published' => [
                            'type' => 'boolean',
                            'index' => 'not_analyzed',
                            'include_in_all' => false
                        ],
                        'language' => [
                            'type' => 'string',
                            'include_in_all' => false
                        ],
                        'parent_id' => [
                            'type' => 'integer',
                            'include_in_all' => false
                        ],
                        'title' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer'
                        ],
                        'text' => [
                            'type' => 'string',
                            //'analyzer' => 'grom_analyzer',
                            //"index_options" => "offsets",
                            //"term_vector" => "with_positions_offsets"
                        ],
                        'metakey' => [
                            'type' => 'string'
                        ],
                        'metadesc' => [
                            'type' => 'string'
                        ],
                        'tags' => [
                            'type' => 'string',
                            'index_name' => 'tag'
                        ],
                        'date' => [
                            "type" => "date"
                        ]
                    ]
                ]

            ]
        ]);

        echo "Index $index created.\n";

        $documents = [
            'gromver\platform\basic\common\models\elasticsearch\Page',
            'gromver\platform\basic\common\models\elasticsearch\Post',
            'gromver\platform\basic\common\models\elasticsearch\Category',
        ];

        foreach ($documents as $documentClass) {
            echo "Uploading {$documentClass} models.\n";
            $completed = $this->upload($documentClass);
            echo "{$completed} items uploaded.\n";
        }
    }

    /**
     * @param $documentClass \gromver\platform\basic\common\models\elasticsearch\ActiveDocument
     * @return int
     * @throws Exception
     */
    public function upload($documentClass)
    {
        $bulk = '';
        /** @var \yii\db\ActiveRecord $modelClass */
        $modelClass = $documentClass::model();
        /** @var \gromver\platform\basic\common\models\elasticsearch\ActiveDocument $document */
        $document = new $documentClass;
        $query = $modelClass::find();
        //древовидные модели, не должны индексировать рутовый элемент
        if ($query->hasMethod('noRoots')) {
            $query->noRoots();
        }
        foreach ($query->each() as $model) {
            /** @var \yii\db\ActiveRecord $model */
            $action = Json::encode([
                "index" => [
                    "_id" => $model->getPrimaryKey(),
                    "_type" => $documentClass::type(),
                    "_index" => $documentClass::index(),
                ],
            ]);

            $document->loadModel($model);
            $data = Json::encode($document->toArray());
            $bulk .= $action . "\n" . $data . "\n";
        }

        if (empty($bulk)) {
            return 0;
        }

        //todo непонятный касяк с кодировкой, при сохранении через актив рекорд - норм, через пакетный способ - хрень
        $url = [$documentClass::index(), $documentClass::type(), '_bulk'];
        $response = \yii\elasticsearch\ActiveRecord::getDb()->post($url, [], $bulk);
        $n = 0;
        $errors = [];
        foreach ($response['items'] as $item) {
            if (isset($item['index']['status']) && in_array($item['index']['status'], [200, 201])) {
                $n++;
            } else {
                $errors[] = $item['index'];
            }
        }
        if (!empty($errors) || isset($response['errors']) && $response['errors']) {
            throw new Exception(__METHOD__ . ' failed inserting '. $modelClass .' model records.', $errors);
        }

        return $n;
    }

    public function down()
    {
        $index = \gromver\platform\basic\common\models\elasticsearch\ActiveDocument::index();

        \yii\elasticsearch\ActiveRecord::getDb()->createCommand()->deleteIndex($index);//->deleteAllIndexes();//

        echo "Index $index are deleted successfully.";
    }
}
