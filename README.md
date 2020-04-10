# yii2-redis-pub-sub

This package is a component for **Yii2**. It realise a functions for **Redis** for
publishing messages and subscribing to channels.

## How to install 

``` php
composer require --prefer-dist hollisho/yii2-redis-pub-sub
```

Or add this string to *composer.json*
``` php
"lanfix/yii2-redis-pub-sub": "*"
```

## Set up

Firstly add to configure file ```web.php``` this code
``` php
'redisPubSub' => [
    'class' => 'lanfix\redis_pub_sub\RedisPubSub',
    'connect' => [
        'class' => 'yii\redis\Connection',
        'hostname' => 'localhost',
        'port' => 6379,
        'database' => 0,
        'password' => ''
    ]
],
```

## Usage

Subscribe to Redis channel
``` php
Yii::$app->redisPubSub->subscribe('my-channel-name', function($instance, $channelName, $message) {
    var_dump($message);
});
```

And sending message to this channel
``` php
Yii::$app->redisPubSub->publish('my-channel-name', 'Hello! How are you?');
```
