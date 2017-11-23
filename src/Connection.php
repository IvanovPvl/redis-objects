<?php

declare(strict_types=1);

namespace Redis;

/**
 * Class Connection
 * @package Redis
 */
class Connection
{
    /** @var \Redis */
    private $redis;

    public function __construct(array $options)
    {
        $this->redis = new \Redis();
        $host = array_get($options, 'host');
        $port = array_get($options, 'port', 6379);
        $timeout = array_get($options, 'timeout', 0.0);
        $retryInterval = array_get($options, 'retryInterval', 0);

        if (!$this->redis->connect($host, $port, $timeout, "", $retryInterval)) {
            throw new \RedisException('Unable to connect');
        }
    }

    /**
     * @return \Redis
     */
    public function getRedis(): \Redis
    {
        return $this->redis;
    }
}