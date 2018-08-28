<?php
declare(strict_types=1);

namespace Tale\Collection;

use Tale\AbstractCollection;

class Sequence extends AbstractCollection
{
    use SequenceTrait;

    /**
     * Sequence constructor.
     * @param iterable $iterable
     */
    public function __construct(iterable $iterable = [])
    {
        $this->setIterable($iterable);
    }
}
