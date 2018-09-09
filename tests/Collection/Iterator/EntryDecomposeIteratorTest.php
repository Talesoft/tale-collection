<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Collection\Iterator\EntryComposeIterator;
use Tale\Collection\Iterator\EntryDecomposeIterator;
use function Tale\queue;
use function Tale\set;
use function Tale\stack;

/**
 * @coversDefaultClass \Tale\Collection\Iterator\EntryDecomposeIterator
 */
class EntryDecomposeIteratorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::key
     * @covers ::current
     */
    public function testIteration(): void
    {
        $iterator = new EntryDecomposeIterator(new \ArrayIterator([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']]));
        self::assertSame(range('a', 'd'), iterator_to_array($iterator));
    }
}
