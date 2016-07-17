External Target for Logging in Yii2
===================================

This extension provides the logging target for the [Yii framework 2.0](http://www.yiiframework.com).

[![Latest Stable Version](https://poser.pugx.org/nfedoseev/yii2-external-target/v/stable)](https://packagist.org/packages/nfedoseev/yii2-external-target)
[![Total Downloads](https://poser.pugx.org/nfedoseev/yii2-external-target/downloads)](https://packagist.org/packages/nfedoseev/yii2-external-target)
[![Yii2](https://img.shields.io/badge/Powered_by-Yii_Framework-green.svg?style=flat)](http://www.yiiframework.com/)
[![License](https://poser.pugx.org/nfedoseev/yii2-external-target/license)](https://packagist.org/packages/nfedoseev/yii2-external-target)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist nfedoseev/yii2-external-target
```

or add

```
"nfedoseev/yii2-external-target": "*"
```

to the require section of your composer.json.


Configuring application
-----------------------

After extension is installed you need to setup log target class:

```php
    'components' => [
        'errorHandler' => [
            'errorAction' => 'site/error',
            'class' => 'nfedoseev\yii2\ExternalTarget\ErrorHandler',
        ],
        'logClient' => [
            'class' => 'nfedoseev\yii2\ExternalTarget\LogClient',
            'baseUrl' => 'your_logger_collector_url',
            'site' => 'your_site_identity',
            'user_id' => 'id',
            'ignore_statuses' => [200]
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'nfedoseev\yii2\ExternalTarget\LogTarget',
                    'levels' => ['error'],
                    'categories' => ['yii\db\*', 'app\*'],
                ],
                ...
            ],
        ],
        ...
    ],
...
```