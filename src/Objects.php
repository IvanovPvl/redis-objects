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
    /** @var Counter[] */
    private $counters = [];

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
    public function setConnection(Connection $connection): void
    {
        foreach ($this->counters as $counter) {
            $counter->setConnection($connection);
        }
    }

    /**
     * @param string $field
     * @param int    $initial
     */
    public function addCounter(string $field, int $initial = 0): void
    {
        $counter = new Counter(get_called_class(), $this->id, $field, $initial);
        $this->counters[$field] = $counter;
    }
}