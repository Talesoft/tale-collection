<?php
declare(strict_types=1);

namespace Tale\Collection\Iterator;

use Tale\Iterator\IndexIterator;

class EntryIterator extends IndexIterator
{
    public function key()
    {
        return $this->getIndex();
    }

    public function current()
    {
        return [parent::key(), parent::current()];
    }
}