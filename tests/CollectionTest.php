<?php
declare(strict_types=1);

namespace Tale\Collection;

use function Tale\collection;
use PHPUnit\Framework\TestCase;
use Tale\Iterator\CallbackMapIterator;

/**
 * @coversDefaultClass \Tale\Collection
 */
class CollectionTest extends TestCase
{
    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::getKeys
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getKeys
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetKeys(): void
    {
        $collection = collection(array_flip(range('a', 'd')));
        self::assertSame(['a', 'b', 'c', 'd'], $collection->getKeys()->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::getValues
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getValues
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetValues(): void
    {
        $collection = collection(['test' => 'a', 6 => 'b', 234 => 'c', 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $collection->getValues()->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::getEntries
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::getEntries
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testGetEntries(): void
    {
        $collection = collection(['test' => 'a', 6 => 'b', 234 => 'c', 'abc' => 'd']);
        self::assertSame([['test', 'a'], [6, 'b'], [234, 'c'], ['abc', 'd']], $collection->getEntries()->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::offsetExists
     * @covers ::ensureArray
     */
    public function testOffsetExists(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertTrue($collection->offsetExists('a'));
        self::assertTrue($collection->offsetExists('b'));
        self::assertTrue($collection->offsetExists('c'));
        self::assertFalse($collection->offsetExists('d'));
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::offsetGet
     */
    public function testOffsetGet(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertSame(1, $collection->offsetGet('a'));
        self::assertSame(2, $collection->offsetGet('b'));
        self::assertSame(3, $collection->offsetGet('c'));
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::offsetGet
     * @covers ::ensureArray
     * @expectedException \OutOfRangeException
     */
    public function testOffsetGetThrowsExceptionOnInvalidOffset(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertSame(1, $collection->offsetGet('d'));
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::offsetSet
     * @covers ::offsetGet
     * @covers ::ensureArray
     */
    public function testOffsetSet(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        $collection->offsetSet('b', 5);
        $collection->offsetSet('d', 10);
        $collection->offsetSet(null, 15);
        self::assertSame(5, $collection->offsetGet('b'));
        self::assertSame(10, $collection->offsetGet('d'));
        self::assertSame(15, $collection->offsetGet(0));
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::offsetUnset
     * @covers ::ensureArray
     */
    public function testOffsetUnset(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        $collection->offsetUnset('b');
        $collection->offsetUnset('d'); //Shouldn't do anything
        self::assertFalse($collection->offsetExists('b'));
        self::assertFalse($collection->offsetExists('d'));
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::count
     * @covers ::ensureArray
     */
    public function testCount(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertSame(3, $collection->count());
        $collection = collection($this->generate(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertSame(3, $collection->count());
        $collection->offsetSet(null, 'test');
        self::assertSame(4, $collection->count());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::forEach
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::forEach
     * @covers \Tale\AbstractCollection::getIterator
     */
    public function testForEach(): void
    {
        $string = '';
        collection(range('a', 'd'))->forEach(function (string $char, int $key) use (&$string) {
            $string .= "$char-$key";
        });
        self::assertSame('a-0b-1c-2d-3', $string);
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::map
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::map
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testMap(): void
    {
        $mappedValues = collection([6, 5, 4, 3, 2])->map(function (int $value, int $key) {
            return $value * $key;
        });
        self::assertSame([0, 5, 8, 9, 8], $mappedValues->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::filter
     * @covers ::getValues
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::filter
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testFilter(): void
    {
        $filteredValues = collection(range(0, 10))->filter(function (int $value, int $key) {
            return $key !== 3 && $value !== 8;
        });
        self::assertSame([0, 1, 2, 4 => 4, 5, 6, 7, 9 => 9, 10], $filteredValues->toArray());
        self::assertSame([0, 1, 2, 4, 5, 6, 7, 9, 10], $filteredValues->getValues()->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::reduce
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::reduce
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testReduce(): void
    {
        $result = collection(range(1, 10))->reduce(function (int $carry, int $value) {
            return $carry + $value;
        }, 15);
        self::assertSame(70, $result);
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::sort
     * @covers ::toArray
     */
    public function testSort(): void
    {
        $collection = collection(range(10, 1));
        $collection->sort(function (int $a, int $b) {
            return $a <=> $b;
        });
        self::assertSame(range(1, 10), $collection->toArray());

        $collection = collection(range('z', 'a'));
        $collection->sort();
        self::assertSame(range('a', 'z'), $collection->toArray());

        $collection = collection(range('a', 'z'));
        $collection->sort(function (string $a, string $b) {
            return -($a <=> $b);
        });
        self::assertSame(range('z', 'a'), $collection->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::serialize
     * @covers ::ensureArray
     */
    public function testSerialize(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertSame('a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}', $collection->serialize());
        $collection = collection($this->generate(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertSame('a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}', $collection->serialize());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::unserialize
     * @covers ::ensureArray
     */
    public function testUnserialize(): void
    {
        $collection = collection();
        $collection->unserialize('a:3:{s:1:"a";i:1;s:1:"b";i:2;s:1:"c";i:3;}');
        self::assertSame(['a' => 1, 'b' => 2, 'c' => 3], $collection->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::jsonSerialize
     * @covers ::ensureArray
     */
    public function testJsonSerialize(): void
    {
        $collection = collection(['a' => 1, 'b' => 2, 'c' => 3]);
        self::assertSame(['a' => 1, 'b' => 2, 'c' => 3], $collection->jsonSerialize());
        $collection = collection($this->generate(['a' => 1, 'b' => 2, 'c' => 3]));
        self::assertSame(['a' => 1, 'b' => 2, 'c' => 3], $collection->jsonSerialize());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::flip
     * @covers ::chain
     * @covers ::toArray
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::flip
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testFlip(): void
    {
        $flipped = collection(range('a', 'd'))->flip();
        self::assertSame(['a' => 0, 'b' => 1, 'c' => 2, 'd' => 3], $flipped->toArray());

        $collection = collection(['a' => 2, 'b' => 2, 'c' => 2, 'd' => 1]);
        //Flip, do something with it, flip again
        $flipped = $collection
            ->flip()
            ->map(function (string $key) {
                return strtoupper($key);
            })
            ->flip();
        //The duplicated values shouldn't lead to overwritten keys when doing iterator stuff only
        //Through the flip we can essentially map keys instead of values
        self::assertSame(['A' => 2, 'B' => 2, 'C' => 2, 'D' => 1], $flipped->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::join
     * @covers ::toArray
     * @covers ::__toString
     * @covers \Tale\AbstractCollection::join
     */
    public function testJoin(): void
    {
        $collection = collection(range('a', 'd'));
        self::assertSame('a,b,c,d', $collection->join());
        self::assertSame('a,b,c,d', (string)$collection);
        self::assertSame('a:b:c:d', $collection->join(':'));
        self::assertSame('0;a:1;b:2;c:3;d', $collection->join(':', ';'));

        $collection = collection($this->generate(range('a', 'd')));
        self::assertSame('a,b,c,d', $collection->join());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::chain
     * @covers ::toArray
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testChain(): void
    {
        $collection = collection(range('a', 'z'))
            ->chain(\RegexIterator::class, '/^[a-f]$/')
            ->chain(CallbackMapIterator::class, function (string $char) {
                return strtoupper($char);
            });

        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F'], $collection->toArray());
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::chain
     * @covers ::toArray
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     * @expectedException \InvalidArgumentException
     */
    public function testChainThrowsExceptionOnInvalidIterator(): void
    {
        collection(range('a', 'z'))->chain(\stdClass::class, '/^[a-f]$/');
    }

    /**
     * @covers \Tale\collection
     * @covers ::__construct
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $collection = collection(range('a', 'd'));
        self::assertSame(['a', 'b', 'c', 'd'], $collection->toArray());
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
