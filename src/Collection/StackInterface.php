<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\CollectionInterface;

interface StackInterface extends CollectionInterface
{
    public function push($item): void;
    public function pop();
    public function unshift($item): void;
    public function shift();
}
