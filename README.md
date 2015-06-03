Grom Platform
=============
Платформа для разработки веб приложений, на основе Yii2 Basic application template

## Демо сайт
http://demo.gromver.com

## Возможности

* Модули: авторизация, пользователи, меню, страницы, новости, теги, поиск, медиа менеджер и т.д.
* Древовидные категории новостей.
* Встроенная система контроля версий документов.
* Поиск
* SEO-friendly адреса страниц (ЧПУ)

Установка
------------

Через [composer](http://getcomposer.org/download/).

Запустить в командной строке проекта

```
php composer.phar require --prefer-dist gromver/yii2-platform-basic "*"
```

или добавить

```
"gromver/yii2-platform-basic": "*"
```

в require секцию `composer.json` файла.


#### Настройка Grom Platform
Заменяем веб и консольное приложения на соответсвующие из данного расширения. Для этого правим файлы:

* /web/index.php
```
  (new \gromver\platform\basic\Application($config))->run();  //(new yii\web\Application($config))->run();
```
* /yii.php
```
  $application = new \gromver\platform\basic\console\Application($config);  //yii\console\Application($config);
```

Нужно отредактировать конфиг приложения: /config/web.php

``` 
[
  'components' => [
      'user' => [
          //'identityClass' => 'app\models\User',  //закоментировать или удалить эту строку
          'enableAutoLogin' => true,
      ],
    ]
]
```
#### Создание таблиц, папок и первоначальных настроек приложения
Для начала нужно убедится, что в корне приложения создана папка migrations, иначе будет ошибка
Error: Migration failed. Directory specified in migrationPath doesn't exist.

    php yii migrate

В результате применения миграций будут добавлены папки
 * /web/upload  - для хранения изображений прикрепляемых к статьям и категориям
 * /web/files   - для хранения файлов медиа менеджера

## Поиск
По умолчанию используется mysql поиск, но можно подключить альтернативные поисковые модули
#### Подключение Elasticsearch поиска (опционально)
* Установить [Elasticsearch](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html)
* Подключаем поисковые модули еластиксерча. Настрайваем консольное приложение, правим /config/console.php
```
'modules' => [
    'grom' => [
        'modules' => [
            'search' => [
                'modules' => [
                    'elastic' => [
                        'class' => 'gromver\platform\basic\modules\search\modules\elastic\Module',
                        'elasticsearchIndex' => 'myapp' //название индекса
                    ]
                ]
            ]
        ]
    ],
],
```
Веб конфиг, правим /config/web.php
```
'modules' => [
    'grom' => [
        'modules' => [
            'search' => [
                'modules' => [
                    'elastic' => [
                        'class' => 'gromver\platform\basic\modules\search\modules\elastic\Module',
                        'elasticsearchIndex' => 'myapp' //название индекса
                    ]
                ]
            ]
        ]
    ],
],
```
* Применяем миграцию для Elasticsearch
```
  php yii migrate
```
