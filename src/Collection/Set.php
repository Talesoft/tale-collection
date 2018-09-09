<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Set extends AbstractCollection implements SetInterface
{
    use SequenceTrait {
        offsetSet as private sequenceOffsetSet;
    }

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
        $this->offsetSet(null, $item);
    }

    public function offsetSet($offset, $value): void
    {
        if ($this->has($value)) {
            return;
        }
        $this->sequenceOffsetSet($offset, $value);
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
