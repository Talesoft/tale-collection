<?php
declare(strict_types=1);

namespace Tale;

use Tale\Collection\Iterator\EntryComposeIterator;
use Tale\Iterator\CallbackFilterIterator;
use Tale\Iterator\CallbackMapIterator;
use Tale\Iterator\FlipIterator;
use Tale\Iterator\IterableIterator;
use Tale\Iterator\KeyIterator;
use Tale\Iterator\ValueIterator;

abstract class AbstractCollection implements CollectionInterface
{
    abstract protected function getIterable(): iterable;

    public function getKeys(): CollectionInterface
    {
        return $this->chain(KeyIterator::class);
    }

    public function getValues(): CollectionInterface
    {
        return $this->chain(ValueIterator::class);
    }

    public function getEntries(): CollectionInterface
    {
        return $this->chain(EntryComposeIterator::class);
    }

    public function getIterator()
    {
        return new IterableIterator($this->getIterable());
    }

    public function forEach(callable $handler): void
    {
        $iterator = $this->getIterator();
        foreach ($iterator as $key => $value) {
            $handler($value, $key, $iterator);
        }
    }

    public function map(callable $mapper): CollectionInterface
    {
        return $this->chain(CallbackMapIterator::class, $mapper);
    }

    public function filter(callable $filter): CollectionInterface
    {
        return $this->chain(CallbackFilterIterator::class, $filter);
    }

    public function reduce(callable $reducer, $initialValue = null)
    {
        $carry = $initialValue;
        $iterator = $this->getIterator();
        foreach ($iterator as $key => $value) {
            $carry = $reducer($carry, $value, $key, $iterator);
        }
        return $carry;
    }

    public function flip(): CollectionInterface
    {
        return $this->chain(FlipIterator::class);
    }

    public function chain(string $iteratorClassName, ...$args): CollectionInterface
    {
        if (!is_subclass_of($iteratorClassName, \IteratorIterator::class, true)) {
            throw new \InvalidArgumentException('Passed class name is not a valid IteratorIterator class');
        }
        return new Collection(new $iteratorClassName(
            $this->getIterator(),
            ...$args
        ));
    }

    public function join(string $delimiter = ',', string $keyDelimiter = null): string
    {
        $iterable = $this->getIterable();
        $values = $iterable instanceof \Traversable
            ? iterator_to_array($iterable)
            : (array)$iterable;
        if ($keyDelimiter !== null) {
            foreach ($values as $key => $value) {
                $values[$key] = "{$key}{$keyDelimiter}{$value}";
            }
        }
        return implode($delimiter, $values);
    }

    public function toArray(): array
    {
        $iterable = $this->getIterable();
        return $iterable instanceof \Traversable
            ? iterator_to_array($iterable)
            : (array)$iterable;
    }

    public function __toString(): string
    {
        return $this->join();
    }
}
