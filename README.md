[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/yii2-request-checksum/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/yii2-request-checksum/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/carono/yii2-request-checksum/v/stable)](https://packagist.org/packages/carono/yii2-request-checksum)
[![Total Downloads](https://poser.pugx.org/carono/yii2-request-checksum/downloads)](https://packagist.org/packages/carono/yii2-request-checksum)
[![License](https://poser.pugx.org/carono/yii2-request-checksum/license)](https://packagist.org/packages/carono/yii2-request-checksum)

Описание
=
Защита форм от подделки от клиента. При несовпадении отправленной формы от пользователя с той, что была сформирована сервером, произойдет ошибка 400

Пользователь может подделать форму, зная имя атрибутов модели, если они помечены как safe и не проверяются дополнительно
![](http://g.recordit.co/2aCVmeF7cL.gif)

Если подключить новый request в компонентах, то при рендеринге HTML, все поля будут сохраняться и при получении данных от пользователя будут сверяться, а при несовпадении, будет выброшена ошибка.
![](http://g.recordit.co/EBCTnhXtGI.gif)


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
        'checksumKey' => 'secret key'
    ],
]
```