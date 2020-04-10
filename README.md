# yii2-redis-pub-sub

This library adds binding functions for **Redis** to publish and subscribe to channels.

## How to install

``` php
composer require --prefer-dist lanfix/yii2-redis-pub-sub
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

To unsubscribe from channel use
``` php
Yii::$app->redisPubSub->unsubscribe('my-channel-name');
```
