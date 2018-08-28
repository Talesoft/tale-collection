<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Stack extends AbstractCollection implements StackInterface
{
    use SequenceTrait;

    public function __construct(iterable $iterable = [])
    {
        $this->setIterable($iterable);
    }

    public function push($item): void
    {
        $this->offsetSet(null, $item);
    }

    public function pop()
    {
        return array_pop($this->items);
    }

    public function unshift($item): void
    {
        array_unshift($this->items, $item);
    }

    public function shift()
    {
        return array_shift($this->items);
    }
}
