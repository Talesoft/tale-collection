<?php
declare(strict_types=1);

namespace Tale\Collection;

trait SequenceTrait
{
    /**
     * @var array
     */
    private $items = [];

    private function setIterable(iterable $iterable): void
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

    public function offsetExists($offset): bool
    {
        $this->validateKey($offset);
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->validateKey($offset);
        if (!isset($this->items[$offset])) {
            throw new \OutOfRangeException("The key {$offset} doesn't exist in this collection");
        }
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->validateKey($offset, true);
        if ($offset === null || !isset($this->items[$offset])) {
            $this->items[] = $value;
            return;
        }
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->validateKey($offset);
        array_splice($this->items, $offset, 1);
    }

    public function count(): int
    {
        return \count($this->items);
    }

    public function sort(callable $comparator = null): void
    {
        usort($this->items, $comparator ?? 'strcmp');
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

    private function validateKey($key, bool $nullAllowed = false): void
    {
        if (!\is_int($key) && (!$nullAllowed || $key !== null)) {
            throw new \InvalidArgumentException('Sequential collections only support integer keys');
        }
    }
}