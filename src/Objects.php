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
    private $connection;

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
        $this->connection = $connection;
        foreach ($this->counters as $counter) {
            $counter->setConnection($connection);
        }
    }

    /**
     * @return \Redis
     */
    public function getRedis(): \Redis
    {
        return $this->connection->getRedis();
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

        $counter = new Counter(get_called_class(), $this->{$this->redisIdField}, $field, $initial);
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