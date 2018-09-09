<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use function Tale\set;
use function Tale\stack;

/**
 * @coversDefaultClass \Tale\Collection\Stack
 */
class StackTest extends TestCase
{
    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::setIterable
     */
    public function testConstruct(): void
    {
        $stack = stack([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
        $stack = stack($this->generate([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']));
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::push
     */
    public function testPush(): void
    {
        $stack = stack(range('a', 'd'));
        self::assertCount(4, $stack);
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
        $stack->push('e');
        self::assertCount(5, $stack);
        self::assertSame(['a', 'b', 'c', 'd', 'e'], $stack->toArray());
        $stack->push('e');
        self::assertCount(6, $stack);
        self::assertSame(['a', 'b', 'c', 'd', 'e', 'e'], $stack->toArray());
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::pop
     */
    public function testPop(): void
    {
        $stack = stack(range('a', 'd'));
        self::assertCount(4, $stack);
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
        $char = $stack->pop();
        self::assertCount(3, $stack);
        self::assertSame(['a', 'b', 'c'], $stack->toArray());
        self::assertSame($char, 'd');
        $char = $stack->pop();
        self::assertCount(2, $stack);
        self::assertSame(['a', 'b'], $stack->toArray());
        self::assertSame($char, 'c');
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::pop
     * @expectedException \OutOfRangeException
     */
    public function testPopThrowsExceptionWhenNoItemsLeft(): void
    {
        $stack = stack(['a']);
        self::assertSame('a', $stack->pop());
        $stack->pop();
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::unshift
     */
    public function testUnshift(): void
    {
        $stack = stack(range('a', 'd'));
        self::assertCount(4, $stack);
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
        $stack->unshift('e');
        self::assertCount(5, $stack);
        self::assertSame(['e', 'a', 'b', 'c', 'd'], $stack->toArray());
        $stack->unshift('e');
        self::assertCount(6, $stack);
        self::assertSame(['e', 'e', 'a', 'b', 'c', 'd'], $stack->toArray());
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::shift
     */
    public function testShift(): void
    {
        $stack = stack(range('a', 'd'));
        self::assertCount(4, $stack);
        self::assertSame(['a', 'b', 'c', 'd'], $stack->toArray());
        $char = $stack->shift();
        self::assertCount(3, $stack);
        self::assertSame(['b', 'c', 'd'], $stack->toArray());
        self::assertSame($char, 'a');
        $char = $stack->shift();
        self::assertCount(2, $stack);
        self::assertSame(['c', 'd'], $stack->toArray());
        self::assertSame($char, 'b');
    }

    /**
     * @covers \Tale\stack
     * @covers ::__construct
     * @covers ::shift
     * @expectedException \OutOfRangeException
     */
    public function testShiftThrowsExceptionWhenNoItemsLeft(): void
    {
        $stack = stack(['a']);
        self::assertSame('a', $stack->shift());
        $stack->shift();
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
