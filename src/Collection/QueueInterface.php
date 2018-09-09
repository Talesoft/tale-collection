<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\CollectionInterface;

interface QueueInterface extends CollectionInterface
{
    public const DIRECTION_LIFO = 0; //Last In - First Out (push in - shift out)
    public const DIRECTION_FIFO = 1; //First In - First Out (unshift in - shift out)
    public const DIRECTION_LILO = 2; //Last In - Last Out (push in - pop out)
    public const DIRECTION_FILO = 3; //First In - Last Out (unshift in - pop out)

    public function getDirection(): int;
    public function enqueue($item): void;
    public function dequeue();
}