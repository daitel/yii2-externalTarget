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
'log' => [
    ...
    'targets' => [
        [
            'class' => 'nfedoseev\yii2\ExternalTarget\HttpTarget',
            'levels' => ['error', 'warning', 'info'],
            'logVars' => [],
            'baseUrl' => 'http://example.com/log',
            'site' => 'your_site_identity',
            'user_id' => 'id',
            'ignore_statuses' => [200]
        ],
        ...
    ],
],
...
```