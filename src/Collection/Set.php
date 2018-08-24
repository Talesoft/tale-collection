<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Set extends AbstractCollection implements SetInterface
{
    /**
     * @var array
     */
    private $items;

    public function __construct(iterable $iterable = [])
    {
        $this->items = array_values(
            $iterable instanceof \Traversable
            ? iterator_to_array($iterable)
            : (array)$iterable
        );
    }

    protected function getIterable(): iterable
    {
        return $this->items;
    }

    public function has($item): bool
    {
        return \in_array($item, $this->items, true);
    }

    public function add($item): void
    {
        $this->offsetSet(null, $item);
    }

    public function remove($item): void
    {
        $offset = array_search($item, $this->items, true);
        if ($offset === -1) {
            return;
        }
        $this->offsetUnset($offset);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        if ($offset === null || !isset($this->items[$offset])) {
            $this->items[] = $value;
        }
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        array_splice($this->items, $offset, 1);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function sort(callable $comparator): void
    {
        usort($this->items, $comparator);
    }

    public function join(string $delimiter = ','): string
    {
        return implode($delimiter, $this->items);
    }

    public function serialize(): string
    {
        return serialize($this->items);
    }

    public function unserialize($serialized): void
    {
        $this->items = unserialize($serialized);
    }

    public function jsonSerialize()
    {
        return $this->items;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->items;
    }
}
