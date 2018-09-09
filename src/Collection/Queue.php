<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Queue extends AbstractCollection implements QueueInterface
{
    use SequenceTrait;

    private $direction;

    public function __construct(iterable $iterable = [], int $direction = self::DIRECTION_LIFO)
    {
        if ($direction > self::DIRECTION_FILO) {
            throw new \InvalidArgumentException('Passed direction is not a valid queue direction');
        }
        $this->setIterable($iterable);
        $this->direction = $direction;
    }

    /**
     * @return int
     */
    public function getDirection(): int
    {
        return $this->direction;
    }

    public function enqueue($item): void
    {
        if ($this->direction === self::DIRECTION_LIFO || $this->direction === self::DIRECTION_LILO) {
            $this->offsetSet(null, $item);
            return;
        }

        array_unshift($this->items, $item);
    }

    public function dequeue()
    {
        if ($this->count() < 1) {
            throw new \OutOfRangeException('Failed to dequeue: No items in queue');
        }

        if ($this->direction === self::DIRECTION_FILO || $this->direction === self::DIRECTION_LILO) {
            return array_pop($this->items);
        }

        return array_shift($this->items);
    }
}
