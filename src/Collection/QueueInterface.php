<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\CollectionInterface;

interface QueueInterface extends CollectionInterface
{
    public const DIRECTION_LIFO = 0;
    public const DIRECTION_FIFO = 1;
    public const DIRECTION_LILO = 2;
    public const DIRECTION_FILO = 3;

    public function getDirection(): int;
    public function enqueue($item): void;
    public function dequeue();
}