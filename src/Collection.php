<?php
declare(strict_types=1);

namespace Tale;

class Collection extends AbstractCollection
{
    /**
     * @var iterable
     */
    private $iterable;

    /**
     * ArrayCollection constructor.
     * @param $iterable
     */
    public function __construct(iterable $iterable = [])
    {
        $this->iterable = $iterable;
    }

    public function offsetExists($offset): bool
    {
        $this->ensureArray();
        return isset($this->iterable[$offset]);
    }

    public function offsetGet($offset)
    {
        $this->ensureArray();
        if (!isset($this->iterable[$offset])) {
            throw new \OutOfRangeException("The key {$offset} doesn't exist in this collection");
        }
        return $this->iterable[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->ensureArray();
        if ($offset === null) {
            $this->iterable[] = $value;
            return;
        }
        $this->iterable[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        $this->ensureArray();
        unset($this->iterable[$offset]);
    }

    public function count(): int
    {
        $this->ensureArray();
        return \count($this->iterable);
    }

    public function sort(callable $comparator = null): void
    {
        $this->ensureArray();
        usort($this->iterable, $comparator ?? 'strcmp');
    }

    public function serialize(): string
    {
        $this->ensureArray();
        return serialize($this->iterable);
    }

    public function unserialize($serialized): void
    {
        $this->iterable = unserialize($serialized);
    }

    public function jsonSerialize()
    {
        $this->ensureArray();
        return $this->iterable;
    }

    protected function getIterable(): iterable
    {
        return $this->iterable;
    }

    private function ensureArray(): void
    {
        if ($this->iterable instanceof \Traversable) {
            $this->iterable = iterator_to_array($this->iterable);
        }
    }
}
