<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\CollectionInterface;

interface SetInterface extends CollectionInterface
{
    public function has($item): bool;
    public function add($item): void;
    public function remove($item): void;
}
