# yii2-redis-pub-sub

This library adds binding functions for **Redis** to publish and subscribe to channels.

## How to install

You must do it for prepare system to work:
- Compile php with php-redis extension if you use php-fpm or install php-redis
apache module in other case.
- Enable php-redis in *php.ini* ``` extension=redis ```

Next install this library with composer
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
        'hostname' => 'localhost',
        'port' => 6379,
        'password' => ''
    ]
],
```

## Usage

Subscribe to Redis channel  
###### Warning! It function stops your application and wait messages!  
It is desirable to run this function in parallel or in other daemon
``` php
Yii::$app->redisPubSub->subscribe('my-channel-name', function($message) {
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
