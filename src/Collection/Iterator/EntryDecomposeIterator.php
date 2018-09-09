<?php
declare(strict_types=1);

namespace Tale\Collection\Iterator;

use Tale\Iterator\IndexIterator;

class EntryDecomposeIterator extends IndexIterator
{
    public function key()
    {
        return parent::current()[0];
    }

    public function current()
    {
        return parent::current()[1];
    }
}
