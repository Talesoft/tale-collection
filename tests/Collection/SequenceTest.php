<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Iterator\CallbackMapIterator;
use function Tale\map;
use function Tale\sequence;

/**
 * @coversDefaultClass \Tale\Collection\Sequence
 */
class SequenceTest extends TestCase
{
    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     */
    public function testConstruct(): void
    {
        $sequence = sequence([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $sequence->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::getKeys
     * @covers ::chain
     * @covers ::toArray
     * @covers ::setIterable
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getKeys
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetKeys(): void
    {
        $sequence = sequence(array_flip(range('a', 'd')));
        self::assertSame([0, 1, 2, 3], $sequence->getKeys()->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::getValues
     * @covers ::chain
     * @covers ::toArray
     * @covers ::setIterable
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getValues
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetValues(): void
    {
        $sequence = sequence(range('a', 'd'));
        self::assertSame(['a', 'b', 'c', 'd'], $sequence->getValues()->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::getEntries
     * @covers ::chain
     * @covers ::toArray
     * @covers ::setIterable
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getEntries
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetEntries(): void
    {
        $sequence = sequence(range('a', 'd'));
        self::assertSame([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']], $sequence->getEntries()->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetExists
     * @covers ::validateKey
     */
    public function testOffsetExists(): void
    {
        $sequence = sequence(range('a', 'd'));
        self::assertTrue($sequence->offsetExists(0));
        self::assertTrue($sequence->offsetExists(1));
        self::assertTrue($sequence->offsetExists(2));
        self::assertFalse($sequence->offsetExists(4));
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetExists
     * @covers ::validateKey
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetExistsThrowsExceptionOnNonIntegerKey(): void
    {
        sequence(range('a', 'd'))->offsetExists('test');
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetGet
     * @covers ::validateKey
     */
    public function testOffsetGet(): void
    {
        $sequence = sequence(range('a', 'd'));
        self::assertSame('a', $sequence->offsetGet(0));
        self::assertSame('b', $sequence->offsetGet(1));
        self::assertSame('c', $sequence->offsetGet(2));
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetExists
     * @covers ::validateKey
     * @expectedException \InvalidArgumentException
     */
    public function testOffsetGetThrowsExceptionOnNonIntegerKey(): void
    {
        sequence(range('a', 'd'))->offsetGet('test');
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetGet
     * @covers ::validateKey
     * @expectedException \OutOfRangeException
     */
    public function testOffsetGetThrowsExceptionOnInvalidOffset(): void
    {
        sequence(range('a', 'd'))->offsetGet(4);
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetSet
     * @covers ::offsetGet
     * @covers ::validateKey
     */
    public function testOffsetSet(): void
    {
        $sequence = sequence(range('a', 'd'));
        $sequence->offsetSet(0, 'e');
        $sequence->offsetSet(10, 'f');
        $sequence->offsetSet(null, 'g');
        self::assertSame('e', $sequence->offsetGet(0));
        self::assertSame('f', $sequence->offsetGet(4));
        self::assertSame('g', $sequence->offsetGet(5));
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::offsetUnset
     * @covers ::validateKey
     */
    public function testOffsetUnset(): void
    {
        $sequence = sequence(range('a', 'd'));
        $sequence->offsetUnset(0);
        $sequence->offsetUnset(4); //Shouldn't do anything
        self::assertTrue($sequence->offsetExists(0)); //Unset keys move to the bottom, so 0 still exists
        self::assertFalse($sequence->offsetExists(3)); //But 3 shouldn't exist anymore, we're left with ['b', 'c', 'd']
        self::assertFalse($sequence->offsetExists(4));
        self::assertSame(['b', 'c', 'd'], $sequence->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::count
     */
    public function testCount(): void
    {
        $sequence = sequence(range('a', 'c'));
        self::assertSame(3, $sequence->count());
        $sequence = sequence($this->generate(range('a', 'c')));
        self::assertSame(3, $sequence->count());
        $sequence->offsetSet(null, 'd');
        self::assertSame(4, $sequence->count());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::sort
     * @covers ::toArray
     */
    public function testSort(): void
    {
        $sequence = sequence(range(10, 1));
        $sequence->sort(function (int $a, int $b) {
            return $a <=> $b;
        });
        self::assertSame(range(1, 10), $sequence->toArray());

        $sequence = sequence(range('z', 'a'));
        $sequence->sort();
        self::assertSame(range('a', 'z'), $sequence->toArray());

        $sequence = sequence(range('a', 'z'));
        $sequence->sort(function (string $a, string $b) {
            return -($a <=> $b);
        });
        self::assertSame(range('z', 'a'), $sequence->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        $sequence = sequence(range('a', 'd'));
        self::assertSame('a:4:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;s:1:"d";}', $sequence->serialize());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::unserialize
     */
    public function testUnserialize(): void
    {
        $sequence = sequence();
        $sequence->unserialize('a:4:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;s:1:"d";}');
        self::assertSame(range('a', 'd'), $sequence->toArray());
    }

    /**
     * @covers \Tale\sequence
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $sequence = sequence(range('a', 'c'));
        self::assertSame(range('a', 'c'), $sequence->jsonSerialize());
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
