<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\CollectionInterface;

interface MapInterface extends CollectionInterface
{
    public function has($key): bool;
    public function set($key, $value): void;
    public function get($key);
    public function remove($key): void;
}