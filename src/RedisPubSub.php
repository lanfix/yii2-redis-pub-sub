<?php

namespace lanfix\redis_pub_sub;

use Redis;
use yii\base\Exception;
use yii\base\Component;

/**
 * @author lanfix
 */
class RedisPubSub extends Component
{

    /**
     * @var $redis Redis
     */
    private $redis;
    public $connect;

    public function init()
    {
        $this->redis = new Redis();
        $this->redis->connect($this->connect['hostname'], $this->connect['port'], $this->connect['connectionTimeout']);
        $this->connect['password'] && $this->redis->auth($this->connect['password']);
    }

    public function setOptReadTimeout($timeout)
    {
        $this->redis->setOption(Redis::OPT_READ_TIMEOUT, $timeout);
    }

    public function publish($channel, $data)
    {
        if(is_string($channel)) {
            return $this->redis->publish($channel, serialize($data));
        }
        if(!is_array($channel)) {
            throw new Exception('invalid queue');
        }
        try {
            foreach ($channel as $item) {
                $this->redis->publish($item, serialize($data));
            }
        }
        catch (Exception $e) {
            throw $e;
        }
        return true;
    }

    public function subscribe($channel, $callback)
    {
        if(!is_array($callback) && !is_string($callback) && !is_callable($callback)) {
            throw new Exception('invalid callback');
        }
        if(is_string($channel)) {
            $channel = [$channel];
        }
        return $this->redis->subscribe($channel, $callback);
    }

    public function __call($name, $params)
    {
        return call_user_func_array([$this->redis, $name], $params);
    }

}