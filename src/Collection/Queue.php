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
        switch ($this->direction) {
            case self::DIRECTION_LIFO:
            case self::DIRECTION_LILO:
                $this->offsetSet(null, $item);
                break;
            case self::DIRECTION_FILO:
            case self::DIRECTION_FIFO:
                array_unshift($this->items, $item);
                break;
        }

        throw new \RuntimeException('Failed to enqueue: Unknown direction');
    }

    public function dequeue()
    {
        switch ($this->direction) {
            case self::DIRECTION_FILO:
            case self::DIRECTION_LILO:
                return array_pop($this->items);
            case self::DIRECTION_LIFO:
            case self::DIRECTION_FIFO:
                return array_shift($this->items);
        }

        throw new \RuntimeException('Failed to dequeue: Unknown direction');
    }
}
