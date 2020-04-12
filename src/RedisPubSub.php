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
     * Array with settings for connection
     * @var array $connect
     */
    public $connect;

    /**
     * Redis connection session
     * @var $redis Redis
     */
    private $redis;

    public function init()
    {
        $this->redis = new Redis();
        $this->redis->connect($this->connect['hostname'], $this->connect['port'], $this->connect['connectionTimeout']);
        $this->connect['password'] && $this->redis->auth($this->connect['password']);
    }

    public function __call($name, $params)
    {
        // TODO: Move to behaviors and call parent::
        return call_user_func_array([$this->redis, $name], $params);
    }

    /**
     * Set Redis options like key-value
     * Example: [Redis::OPT_READ_TIMEOUT => 2]
     * @param array $options
     */
    public function setOptions(array $options)
    {
        foreach($options as $code => $value) {
            $this->redis->setOption($code, $value);
        }
    }

    /**
     * Prepare channel(s) name(s) to use
     * @param string|string[] $channel channel(s) name
     * @return array|string
     * @throws Exception
     */
    private function prepareChannelName($channel)
    {
        if(is_string($channel)) {
            $channel = [$channel];
        }
        elseif(is_array($channel)) {
            foreach($channel as $item) {
                if(!is_string($item)) {
                    throw new Exception('Invalid channel name');
                }
            }
        }
        else {
            throw new Exception('Invalid channel name');
        }
        return $channel;
    }

    /**
     * Publish message to channel
     * @param string|string[] $channel channel(s) name
     * @param mixed $message anything data (string, array, object, number...)
     * @return int number of clients that received the message
     * @throws Exception
     */
    public function publish($channel, $message)
    {
        if(is_string($channel)) {
            return $this->redis->publish($channel, serialize($message));
        }
        elseif(is_array($channel)) {
            $successTimes = 0;
            $clients = 0;
            foreach($channel as $item) {
                if(!is_string($item)) break;
                $clients += $this->redis->publish($item, serialize($message));
                $successTimes++;
            }
            if($successTimes == count($channel)) {
                return $clients;
            }
        }
        throw new Exception('Invalid channel name');
    }

    /**
     * Subscribe to channel and set callback function
     * When a message arrives, the function is called
     * @param string|string[] $channel channel(s) name
     * @param callable $callback function that will be called when a message arrives on the channel
     * @return mixed|null
     * @throws Exception
     */
    public function subscribe($channel, callable $callback)
    {
        if(!is_array($callback) && !is_string($callback) && !is_callable($callback)) {
            throw new Exception('Invalid callback');
        }
        $channel = $this->prepareChannelName($channel);
        return $this->redis->subscribe($channel, function($redis, $chanel, $message) use ($callback) {
            call_user_func_array($callback, [$message]);
        });
    }

    /**
     * Stop listening the channel
     * @param string|string[] $channel channel(s) name
     * @throws Exception
     */
    public function unsubscribe($channel = [])
    {
        $channel = $this->prepareChannelName($channel);
        $this->redis->unsubscribe($channel);
    }

}