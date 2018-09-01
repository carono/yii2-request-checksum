[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/yii2-request-checksum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/yii2-request-checksum/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/carono/yii2-request-checksum/v/stable)](https://packagist.org/packages/carono/yii2-request-checksum)
[![Total Downloads](https://poser.pugx.org/carono/yii2-request-checksum/downloads)](https://packagist.org/packages/carono/yii2-request-checksum)
[![License](https://poser.pugx.org/carono/yii2-request-checksum/license)](https://packagist.org/packages/carono/yii2-request-checksum)

Установка  
=
`composer require carono/yii2-request-checksum`

Настройка
=
В конфиге добавляем класс запроса

```
'components' => [
    'request' => [
        'class' => \carono\checksum\Request::class,
        'cookieValidationKey' => 'secret key'
    ],
]
```