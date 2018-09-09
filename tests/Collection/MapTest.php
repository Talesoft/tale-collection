<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Iterator\CallbackMapIterator;
use function Tale\map;

/**
 * @coversDefaultClass \Tale\Collection\Map
 */
class MapTest extends TestCase
{
    /**
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionOnInvalidMapArray(): void
    {
        $map = map([1, 2]);
    }
    /**
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
        $map = map([['a', 1], ['b', 2], ['c', 3], ['d', 4]]);
        self::assertSame(['a', 'b', 'c', 'd'], $map->getKeys()->toArray());
    }

    /**
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
        $map = map([['test', 'a'], [6, 'b'], [['test', 5], 'c'], [new class {}, 'd']]);
        self::assertSame(['a', 'b', 'c', 'd'], $map->getValues()->toArray());
    }

    /**
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
        $obj = new class {};
        $map = map([['test', 'a'], [['test', 5], 'b'], [$obj, 'c'], ['abc', 'd']]);
        self::assertSame([['test', 'a'], [['test', 5], 'b'], [$obj, 'c'], ['abc', 'd']], $map->getEntries()->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::has
     * @covers ::offsetExists
     */
    public function testHas(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        self::assertTrue($map->has('a'));
        self::assertTrue($map->has($obj));
        self::assertTrue($map->has('c'));
        self::assertFalse($map->has('d'));
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::offsetGet
     */
    public function testOffsetGet(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        self::assertSame(1, $map->get('a'));
        self::assertSame(2, $map->get($obj));
        self::assertSame(3, $map->get('c'));
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::offsetGet
     * @expectedException \OutOfRangeException
     */
    public function testOffsetGetThrowsExceptionOnInvalidOffset(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        self::assertSame(1, $map->get('d'));
    }

    /**
     * @covers ::__construct
     * @covers ::set
     * @covers ::get
     * @covers ::offsetSet
     * @covers ::offsetGet
     */
    public function testOffsetSet(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        $map->set($obj, 5);
        $map->set('d', 10);
        $map->set(null, 15);
        self::assertSame(5, $map->get($obj));
        self::assertSame(10, $map->get('d'));
        self::assertSame(15, $map->get(null));
    }

    /**
     * @covers ::__construct
     * @covers ::remove
     * @covers ::offsetUnset
     */
    public function testOffsetUnset(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        $map->remove($obj);
        $map->remove('d'); //Shouldn't do anything
        self::assertFalse($map->offsetExists($obj));
        self::assertFalse($map->offsetExists('d'));
    }

    /**
     * @covers ::__construct
     * @covers ::count
     */
    public function testCount(): void
    {
        $obj = new class {};
        $map = map([['a', 1], [$obj, 2], ['c', 3]]);
        self::assertSame(3, $map->count());
        $map = map($this->generate([['a', 1], [$obj, 2], ['c', 3]]));
        self::assertSame(3, $map->count());
        $map->offsetSet(null, 'test');
        self::assertSame(4, $map->count());
    }

    /**
     * @covers ::__construct
     * @covers ::forEach
     * @covers ::getIterable
     * @covers \Tale\AbstractCollection::forEach
     * @covers \Tale\AbstractCollection::getIterator
     */
    public function testForEach(): void
    {
        $string = '';
        map([['a', 0], ['b', 1], ['c', 2], ['d', 3]])->forEach(function (array $entry) use (&$string) {
            [$char, $key] = $entry;
            $string .= "$char-$key";
        });
        self::assertSame('a-0b-1c-2d-3', $string);
    }

    /**
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
        $mappedValues = map([[0, 6], [1, 5], [2, 4], [3, 3], [4, 2]])->map(function (array $entry) {
            [$key, $value] = $entry;
            return $key * $value;
        });
        self::assertSame([0, 5, 8, 9, 8], $mappedValues->toArray());
    }

    /**
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
        $filteredValues = map(
            [[0, 0], [1, 1], [2, 2], [3, 3], [4, 4], [5, 5], [6, 6], [7, 7], [8, 8], [9, 9], [10, 10]]
        )->filter(function (array $entry){
            [$key, $value] = $entry;
            return $key !== 3 && $value !== 8;
        });
        self::assertSame(
            [[0, 0], [1, 1], [2, 2], [4, 4], [5, 5], [6, 6], [7, 7], [9, 9], [10, 10]],
            $filteredValues->getValues()->toArray()
        );
    }

    /**
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
        $result = map(
            [[0, 1], [1, 2], [2, 3], [3, 4], [4, 5], [5, 6], [6, 7], [7, 8], [8, 9], [9, 10]]
        )->reduce(function (int $carry, array $entry) {
            [, $value] = $entry;
            return $carry + $value;
        }, 15);
        self::assertSame(70, $result);
    }

    /**
     * @covers ::__construct
     * @covers ::sort
     * @covers ::toArray
     */
    public function testSort(): void
    {
        $map = map(
            [[0, 10], [1, 9], [2, 8], [3, 7], [4, 6], [5, 5], [6, 4], [7, 3], [8, 2], [9, 1]]
        );
        $map->sort();
        self::assertSame(
            [[9, 1], [8, 2], [7, 3], [6, 4], [5, 5], [4, 6], [3, 7], [2, 8], [1, 9], [0, 10]],
            $map->toArray()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::serialize
     */
    public function testSerialize(): void
    {
        $map = map([['a', 1], ['b', 2], ['c', 3]]);
        self::assertSame(
            'a:2:{i:0;a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}i:1;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}',
            $map->serialize()
        );
    }

    /**
     * @covers ::__construct
     * @covers ::unserialize
     */
    public function testUnserialize(): void
    {
        $map = map();
        $map->unserialize('a:2:{i:0;a:3:{i:0;s:1:"a";i:1;s:1:"b";i:2;s:1:"c";}i:1;a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}');
        self::assertSame([['a', 1], ['b', 2], ['c', 3]], $map->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::jsonSerialize
     */
    public function testJsonSerialize(): void
    {
        $map = map([['a', 1], ['b', 2], ['c', 3]]);
        self::assertSame([['a', 1], ['b', 2], ['c', 3]], $map->jsonSerialize());
    }

    /**
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
        //TODO: Flip is a hard one. As anything without an int/string key would yield an invalid iterator
        //all operations on map will always go index => [key, value] instead of key => value
        //For flip, this is very useless and key => value would be a lot more useful, but it would break
        //the consistency of the API
        //The only useful thing testing is of flipping and flipping back will yield the same map again
        $flipped = map(map([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']])->flip()->flip());
        self::assertSame([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']], $flipped->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::join
     * @covers ::toArray
     * @covers ::__toString
     * @covers \Tale\AbstractCollection::join
     */
    public function testJoin(): void
    {
        $map = map([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']]);
        self::assertSame('a,b,c,d', $map->join());
        self::assertSame('a,b,c,d', (string)$map);
        self::assertSame('a:b:c:d', $map->join(':'));
        self::assertSame('0;a:1;b:2;c:3;d', $map->join(':', ';'));

        $map = map($this->generate([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']]));
        self::assertSame('a,b,c,d', $map->join());
    }

    /**
     * @covers ::__construct
     * @covers ::chain
     * @covers ::toArray
     * @covers \Tale\AbstractCollection::getIterator
     * @covers \Tale\AbstractCollection::chain
     */
    public function testChain(): void
    {
        $collection = map([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd'], [4, 'e'], [5, 'f']])
            ->chain(CallbackMapIterator::class, function (array $entry) {
                return strtoupper($entry[1]);
            });

        self::assertSame(['A', 'B', 'C', 'D', 'E', 'F'], $collection->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $collection = map([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']]);
        self::assertSame([[0, 'a'], [1, 'b'], [2, 'c'], [3, 'd']], $collection->toArray());
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
