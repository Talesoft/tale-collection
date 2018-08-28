<?php
declare(strict_types=1);

namespace Tale;

use ArrayAccess;
use Countable;
use IteratorAggregate;

interface CollectionInterface extends IteratorAggregate, ArrayAccess, Countable, \Serializable, \JsonSerializable
{
    public function getKeys(): CollectionInterface;
    public function getValues(): CollectionInterface;
    public function getEntries(): CollectionInterface;

    public function forEach(callable $handler): void;
    public function map(callable $mapper): CollectionInterface;
    public function filter(callable $filter): CollectionInterface;
    public function reduce(callable $reducer, $initialValue = null);
    public function sort(callable $comparator = null): void;
    public function flip(): CollectionInterface;
    public function join(string $delimiter = ',', string $keyDelimiter = null): string;
    public function chain(string $iteratorClassName, ...$args): CollectionInterface;
    public function toArray(): array;
    public function __toString(): string;
}