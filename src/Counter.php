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

    public function __construct(string $fieldName, int $initial = 0, string $modelName = '', $id = 0)
    {
        if ($modelName && $id) {
            $this->key = "$modelName:$id:$fieldName";
        } else {
            $this->key = $fieldName;
        }

        $this->initial = $initial;

        if ($redis = Objects::getCurrentRedis()) {
            $this->redis = $redis;
            $this->redis->setnx($this->key, $this->initial);
        }
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
     * @param int           $by
     * @param callable|null $func
     *
     * @throws \Throwable
     */
    public function increment(int $by = 1, callable $func = null): void
    {
        if ($func) {
            try {
                $this->increment($by);
                $func();
            } catch (\Throwable $e) {
                $this->decrement($by);
                throw $e;
            }
        } else {
            $this->redis->incrBy($this->key, $by);
        }
    }

    /**
     * @param int           $by
     * @param callable|null $func
     *
     * @throws \Throwable
     */
    public function decrement(int $by = 1, callable $func = null): void
    {
        if ($func) {
            try {
                $this->decrement($by);
                $func();
            } catch (\Throwable $e) {
                $this->increment($by);
                throw $e;
            }
        } else {
            $this->redis->decrBy($this->key, $by);
        }
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