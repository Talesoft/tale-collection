<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;
use Tale\Collection;
use Tale\CollectionInterface;

class Map extends AbstractCollection implements MapInterface
{
    /**
     * @var array
     */
    private $keys;

    /**
     * @var array
     */
    private $values;

    public function __construct(iterable $entries = [])
    {
        foreach ($entries as $entry) {
            if (!\is_array($entry) || \count($entry) !== 2) {
                throw new \InvalidArgumentException('Map entries need to be an array of [$key, $value]');
            }
            $this->offsetSet($entry[0], $entry[1]);
        }
    }

    public function getKeys(): CollectionInterface
    {
        return new Collection($this->keys);
    }

    public function getValues(): CollectionInterface
    {
        return new Collection($this->values);
    }

    public function getEntries(): CollectionInterface
    {
        return new Collection($this->getIterable());
    }

    protected function getIterable(): iterable
    {
        foreach ($this->keys as $i => $key) {
            yield [$key, $this->values[$i]];
        }
    }

    public function has($key): bool
    {
        return $this->offsetExists($key);
    }

    public function set($key, $value): void
    {
        $this->offsetSet($key, $value);
    }

    public function remove($key): void
    {
        $this->offsetUnset($key);
    }

    public function offsetExists($offset): bool
    {
        return \in_array($offset, $this->keys, true);
    }

    public function offsetGet($offset)
    {
        $index = \array_search($offset, $this->keys, true);
        if ($index === -1) {
            throw new \OutOfBoundsException('The offset doesn\'t exist in this map');
        }
        return $this->values[$index];
    }

    public function offsetSet($offset, $value): void
    {
        $index = \array_search($offset, $this->keys, true);
        if ($index === -1) {
            $index = \count($this->keys);
            $this->keys[$index] = $offset;
            return;
        }
        $this->values[$index] = $value;
    }

    public function offsetUnset($offset): void
    {
        $index = \array_search($offset, $this->keys, true);
        if ($index === -1) {
            return;
        }
        array_splice($this->keys, $index, 1);
        array_splice($this->values, $index, 1);
    }

    public function count(): int
    {
        return \count($this->keys);
    }

    public function sort(callable $comparator): void
    {
        $sortedValues = $this->values;
        asort($sortedValues);
        $sortedKeys = [];
        foreach ($sortedValues as $i => $value) {
            $sortedKeys[] = $this->keys[$i];
        }
        $this->keys = $sortedKeys;
        $this->values = array_values($sortedValues);
    }

    public function join(string $delimiter = ','): string
    {
        return implode($delimiter, $this->values);
    }

    public function serialize(): string
    {
        return serialize([$this->keys, $this->values]);
    }

    public function unserialize($serialized): void
    {
        [$this->keys, $this->values] = unserialize($serialized);
    }

    public function jsonSerialize()
    {
        return iterator_to_array($this->getEntries());
    }
}
