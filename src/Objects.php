<?php

declare(strict_types=1);

namespace Redis;

/**
 * Trait Objects
 * @package Redis
 * @property $id
 */
trait Objects
{
    /** @var string */
    private $redisIdField = 'id';

    /** @var Counter[] */
    private $counters = [];

    /** @var Connection */
    private static $connection;

    public function __get($name)
    {
        if ($this->counters && key_exists($name, $this->counters)) {
            return $this->counters[$name];
        }

        return null;
    }

    /**
     * @param Connection $connection
     */
    public function setRedis(Connection $connection): void
    {
        self::$connection = $connection;
        foreach ($this->counters as $counter) {
            $counter->setConnection($connection);
        }
    }

    public static function setCurrentRedis(Connection $connection): void
    {
        self::$connection = $connection;
    }

    public static function getCurrentRedis(): \Redis
    {
        return self::$connection->getRedis();
    }

    /**
     * @return \Redis
     */
    public function getRedis(): \Redis
    {
        return self::$connection->getRedis();
    }

    /**
     * @param string $field
     * @param int    $initial
     *
     * @throws \Exception
     */
    public function addCounter(string $field, int $initial = 0): void
    {
        $idValue = $this->{$this->redisIdField};
        if (!$idValue) {
            throw new \Exception("No {$this->redisIdField} provided for model");
        }

        $counter = new Counter($field, $initial, get_called_class(), $this->{$this->redisIdField});
        $this->counters[$field] = $counter;
    }

    /**
     * @param string $redisIdField
     */
    public function setRedisIdField(string $redisIdField)
    {
        $this->redisIdField = $redisIdField;
    }
}