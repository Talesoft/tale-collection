<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Set extends AbstractCollection implements SetInterface
{
    use SequenceTrait;

    public function __construct(iterable $iterable = [])
    {
        $this->setIterable($iterable);
    }

    public function has($item): bool
    {
        return \in_array($item, $this->items, true);
    }

    public function add($item): void
    {
        if ($this->has($item)) {
            return;
        }
        $this->offsetSet(null, $item);
    }

    public function remove($item): void
    {
        $offset = array_search($item, $this->items, true);
        if ($offset === false) {
            return;
        }
        $this->offsetUnset($offset);
    }
}
