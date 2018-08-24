<?php
declare(strict_types=1);

namespace Tale;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use Tale\Collection\MapInterface;
use Tale\Collection\SetInterface;

interface CollectionInterface extends IteratorAggregate, ArrayAccess, Countable, \Serializable, \JsonSerializable
{
    public function getKeys(): CollectionInterface;
    public function getValues(): CollectionInterface;
    public function getEntries(): CollectionInterface;

    public function forEach(callable $handler): void;
    public function map(callable $mapper): CollectionInterface;
    public function filter(callable $filter): CollectionInterface;
    public function reduce(callable $reducer);
    public function sort(callable $comparator): void;
    public function flip(): CollectionInterface;
    public function join(string $delimiter = ','): string;
    public function chain(string $iteratorClassName, ...$args): CollectionInterface;
    public function toArray(): array;
    public function toCollection(): CollectionInterface;
    public function toMap(): MapInterface;
    public function toSet(): SetInterface;
    public function __toString(): string;
}