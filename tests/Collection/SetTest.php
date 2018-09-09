<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use function Tale\set;

/**
 * @coversDefaultClass \Tale\Collection\Set
 */
class SetTest extends TestCase
{
    /**
     * @covers \Tale\set
     * @covers ::__construct
     * @covers ::setIterable
     */
    public function testConstruct(): void
    {
        $set = set([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $set->toArray());
        $set = set($this->generate([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']));
        self::assertSame(['a', 'b', 'c', 'd'], $set->toArray());
    }

    /**
     * @covers \Tale\set
     * @covers ::__construct
     * @covers ::has
     */
    public function testHas(): void
    {
        $set = set(range('a', 'd'));
        self::assertTrue($set->has('a'));
        self::assertTrue($set->has('b'));
        self::assertFalse($set->has('e'));
    }

    /**
     * @covers \Tale\set
     * @covers ::__construct
     * @covers ::add
     * @covers ::has
     * @covers ::offsetSet
     * @covers ::sequenceOffsetSet
     * @covers ::validateKey
     */
    public function testAdd(): void
    {
        $set = set(range('a', 'd'));
        self::assertTrue($set->has('b'));
        self::assertFalse($set->has('e'));
        self::assertCount(4, $set);
        $set->add('e');
        self::assertTrue($set->has('b'));
        self::assertTrue($set->has('e'));
        self::assertCount(5, $set);
        $set->add('b'); //Adding the same entry twice does nothing
        self::assertCount(5, $set);
        self::assertSame(['a', 'b', 'c', 'd', 'e'], $set->toArray());
    }

    /**
     * @covers \Tale\set
     * @covers ::__construct
     * @covers ::remove
     * @covers ::add
     * @covers ::has
     * @covers ::offsetSet
     * @covers ::sequenceOffsetSet
     * @covers ::validateKey
     */
    public function testRemove(): void
    {
        $set = set(range('a', 'd'));
        self::assertTrue($set->has('b'));
        self::assertFalse($set->has('e'));
        self::assertCount(4, $set);
        $set->add('e');
        self::assertTrue($set->has('b'));
        self::assertTrue($set->has('e'));
        self::assertCount(5, $set);
        $set->remove('b');
        $set->remove('something'); //Should do nothing
        self::assertCount(4, $set);
        self::assertFalse($set->has('b'));
        self::assertTrue($set->has('e'));
        self::assertSame(['a', 'c', 'd', 'e'], $set->toArray());
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
