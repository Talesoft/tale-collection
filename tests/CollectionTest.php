<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Iterator\CallbackMapIterator;
use function Tale\collection;

/**
 * @coversDefaultClass \Tale\Collection
 */
class CollectionTest extends TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKeys
     * @covers ::chain
     * @covers ::toArray
     */
    public function testGetKeys(): void
    {
        $collection = collection(array_flip(range('a', 'd')));
        self::assertSame(['a', 'b', 'c', 'd'], $collection->getKeys()->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::getValues
     * @covers ::chain
     * @covers ::toArray
     */
    public function testGetValues(): void
    {
        $collection = collection(['test' => 'a', 6 => 'b', 234 => 'c', 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $collection->getValues()->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::getEntries
     * @covers ::chain
     * @covers ::toArray
     */
    public function testGetEntries(): void
    {
        $collection = collection(['test' => 'a', 6 => 'b', 234 => 'c', 'abc' => 'd']);
        self::assertSame([['test', 'a'], [6, 'b'], [234, 'c'], ['abc', 'd']], $collection->getEntries()->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::forEach
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
     * @covers ::__construct
     * @covers ::map
     * @covers ::chain
     * @covers ::toArray
     */
    public function testMap(): void
    {
        $mappedValues = collection([6, 5, 4, 3, 2])->map(function (int $value, int $key){
            return $value * $key;
        });
        self::assertSame([0, 5, 8, 9, 8], $mappedValues->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::filter
     * @covers ::getValues
     * @covers ::chain
     * @covers ::toArray
     */
    public function testFilter(): void
    {
        $filteredValues = collection(range(0, 10))->filter(function (int $value, int $key){
            return $key !== 3 && $value !== 8;
        });
        self::assertSame([0, 1, 2, 4 => 4, 5, 6, 7, 9 => 9, 10], $filteredValues->toArray());
        self::assertSame([0, 1, 2, 4, 5, 6, 7, 9, 10], $filteredValues->getValues()->toArray());
    }

    /**
     * @covers ::__construct
     * @covers ::reduce
     * @covers ::toArray
     */
    public function testReduce(): void
    {
        $result = collection(range(1, 10))->reduce(function (int $carry, int $value) {
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
     * @covers ::__construct
     * @covers ::flip
     * @covers ::toArray
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
     * @covers ::__construct
     * @covers ::join
     * @covers ::toArray
     * @covers ::__toString
     */
    public function testJoin(): void
    {
        $collection = collection(range('a', 'd'));
        self::assertSame('a,b,c,d', $collection->join());
        self::assertSame('a,b,c,d', (string)$collection);
        self::assertSame('a:b:c:d', $collection->join(':'));
        self::assertSame('0;a:1;b:2;c:3;d', $collection->join(':', ';'));
    }

    /**
     * @covers ::__construct
     * @covers ::chain
     * @covers ::toArray
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
     * @covers ::__construct
     * @covers ::toArray
     */
    public function testToArray(): void
    {
        $collection = collection(range('a', 'd'));
        self::assertSame(['a', 'b', 'c', 'd'], $collection->toArray());
        $values = collection(['a' => 2, 'b' => 2, 'c' => 3])
            ->flip()
            ->map(function (string $key) {
                return strtoupper($key);
            })
            ->flip();

        var_dump($values->toArray());
    }
}
