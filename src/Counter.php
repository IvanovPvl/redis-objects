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

    /** @var int */
    private $initial = 0;

    public function __construct(string $modelName, $id, $fieldName, int $initial = 0)
    {
        $this->key = "$modelName:$id:$fieldName";
        $this->initial = $initial;
    }

    /**
     * @param Connection $connection
     */
    public function setConnection(Connection $connection): void
    {
        $this->redis = $connection->getRedis();
        $this->redis->setnx($this->key, $this->initial);
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
     * @param int $value
     */
    public function reset(int $value = 0): void
    {
        $this->redis->set($this->key, $value);
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return (int)$this->redis->get($this->key);
    }
}