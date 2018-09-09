<?php
declare(strict_types=1);

namespace Tale;

use Tale\Collection\Map;
use Tale\Collection\MapInterface;
use Tale\Collection\Queue;
use Tale\Collection\QueueInterface;
use Tale\Collection\Sequence;
use Tale\Collection\Set;
use Tale\Collection\SetInterface;
use Tale\Collection\Stack;
use Tale\Collection\StackInterface;

function collection(iterable $iterable = []): CollectionInterface
{
    return new Collection($iterable);
}

function map(iterable $iterable = []): MapInterface
{
    return new Map($iterable);
}

function queue(iterable $iterable = [], int $direction = QueueInterface::DIRECTION_LIFO): QueueInterface
{
    return new Queue($iterable, $direction);
}

function sequence(iterable $iterable = []): Sequence
{
    return new Sequence($iterable);
}

function set(iterable $iterable = []): SetInterface
{
    return new Set($iterable);
}

function stack(iterable $iterable = []): StackInterface
{
    return new Stack($iterable);
}