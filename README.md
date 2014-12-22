Grom Platform
=============
Платформа для разработки веб приложений, на основе Yii2 Basic application template

## Демо сайт
http://menst.webfactional.com

## Возможности

* Модули: авторизация, пользователи, меню, страницы, новости, теги, поиск, медиа менеджер и т.д.
* Древовидные категории новостей.
* Встроенная система контроля версий документов.
* Поиск
* SEO-friendly адреса страниц (ЧПУ)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist gromver/yii2-platform-basic "*"
```

or add

```
"gromver/yii2-platform-basic": "*"
```

to the require section of your `composer.json` file.


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
#### Создаем папки
 * /web/upload  - для хранения изображений прикрепляемых к статьям и категориям
 * /web/files   - для хранения файлов медиа менеджера

Не забываем установить этим папкам права на запись
 
#### Добавляем таблицы в БД

    php yii migrate --migrationPath=@gromver/platform/basic/migrations

#### Подключение поиска(опционально)
* Установить [Elasticsearch](http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/_installation.html)
* Подключаем поисковые модули еластиксерча. Настрайваем консольное приложение, правим /config/console.php
```
[
    'modules' => [
        'grom' => [
            'modules' => [
                'search' => [
                    'class' => 'gromver\platform\basic\modules\elasticsearch\Module',
                    'elasticsearchIndex' => 'myapp'	//название индекса
                ]
            ]
        ]
    ],
]
```
Веб конфиг, правим /config/web.php
```
[
    'modules' => [
        'grom' => [
            'modules' => [
                'search' => [
                    'class' => 'gromver\platform\basic\modules\elasticsearch\Module',
                    'elasticsearchIndex' => 'myapp'	//название индекса
                ]
            ]
        ]
    ],
]
```
* Применяем миграцию для Elasticsearch
```
  php yii migrate --migrationPath=@gromver/platform/basic/migrations/elasticsearch
```
