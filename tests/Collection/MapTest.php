<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use Tale\Iterator\CallbackMapIterator;
use Tale\Iterator\FilterIterator;
use Tale\Iterator\MapIterator;
use Tale\Iterator\ValueIterator;

/**
 * @coversDefaultClass \Tale\Collection\Map
 */
class MapTest extends TestCase
{
    /**
     * @covers ::__construct
     */
    public function testGetSet(): void
    {
        $key1 = new class {};
        $key2 = new class {};

        $map = new Map();
        $map->set($key1, 'value 1');
        $map->set($key2, 'value 2');

        self::assertTrue($map->has($key1));
        self::assertSame('value 1', $map->get($key1));
    }
}
