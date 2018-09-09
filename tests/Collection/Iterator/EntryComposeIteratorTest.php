<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Collection\Iterator\EntryComposeIterator;
use function Tale\queue;
use function Tale\set;
use function Tale\stack;

/**
 * @coversDefaultClass \Tale\Collection\Iterator\EntryComposeIterator
 */
class EntryComposeIteratorTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::key
     * @covers ::current
     */
    public function testIteration(): void
    {
        $iterator = new EntryComposeIterator(new \ArrayIterator(range('a', 'd')));
        self::assertSame([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']], iterator_to_array($iterator));
    }
}
