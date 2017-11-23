<?php

declare(strict_types=1);

namespace Redis;

/**
 * Class Counter
 * @package Redis
 */
class Counter
{
    /** @var \Redis */
    private $redis;

    /** @var string */
    private $key;

    public function __construct(string $modelName, int $id, $fieldName)
    {
        $this->key = "$modelName:$id:$fieldName";
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection): void
    {
        $this->redis = $connection->getRedis();
        $this->redis->setnx($this->key, 0);
    }

    /**
     * @param int $by
     */
    public function increment(int $by = 1): void
    {
        $this->redis->incrBy($this->key, $by);
    }

    /**
     * @param int $by
     */
    public function decrement(int $by = 1): void
    {
        $this->redis->decrBy($this->key, $by);
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->redis->get($this->key);
    }
}