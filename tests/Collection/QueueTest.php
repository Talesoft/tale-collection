<?php
declare(strict_types=1);

namespace Tale\Collection;

use PHPUnit\Framework\TestCase;
use function Tale\queue;
use function Tale\set;
use function Tale\stack;

/**
 * @coversDefaultClass \Tale\Collection\Queue
 */
class QueueTest extends TestCase
{
    /**
     * @covers \Tale\queue
     * @covers ::__construct
     * @covers ::setIterable
     * @covers ::getDirection
     */
    public function testConstruct(): void
    {
        $queue = queue([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        self::assertSame($queue->getDirection(), Queue::DIRECTION_LIFO); //Make sure LIFO is the default
        $queue = queue($this->generate([2 => 'a', 4 => 'b', 16 => 'c', 'y' => 'd']));
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        self::assertSame($queue->getDirection(), Queue::DIRECTION_LIFO);
    }

    /**
     * @covers \Tale\queue
     * @covers ::__construct
     * @expectedException \InvalidArgumentException
     */
    public function testConstructThrowsExceptionOnInvalidDirection(): void
    {
        queue(['a'], 4);
    }

    /**
     * @covers \Tale\queue
     * @covers ::__construct
     * @covers ::enqueue
     */
    public function testEnqueue(): void
    {
        //LI
        $queue = queue(range('a', 'd'));
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $queue->enqueue('e');
        self::assertCount(5, $queue);
        self::assertSame(['a', 'b', 'c', 'd', 'e'], $queue->toArray());

        $queue = queue(range('a', 'd'), Queue::DIRECTION_LILO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $queue->enqueue('e');
        self::assertCount(5, $queue);
        self::assertSame(['a', 'b', 'c', 'd', 'e'], $queue->toArray());

        //FI
        $queue = queue(range('a', 'd'), Queue::DIRECTION_FILO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $queue->enqueue('e');
        self::assertCount(5, $queue);
        self::assertSame(['e', 'a', 'b', 'c', 'd'], $queue->toArray());

        $queue = queue(range('a', 'd'), Queue::DIRECTION_FIFO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $queue->enqueue('e');
        self::assertCount(5, $queue);
        self::assertSame(['e', 'a', 'b', 'c', 'd'], $queue->toArray());
    }

    /**
     * @covers \Tale\queue
     * @covers ::__construct
     * @covers ::dequeue
     */
    public function testDequeue(): void
    {
        //LO
        $queue = queue(range('a', 'd'), Queue::DIRECTION_FILO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $char = $queue->dequeue();
        self::assertSame('d', $char);
        self::assertCount(3, $queue);
        self::assertSame(['a', 'b', 'c'], $queue->toArray());

        $queue = queue(range('a', 'd'), Queue::DIRECTION_LILO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $char = $queue->dequeue();
        self::assertSame('d', $char);
        self::assertCount(3, $queue);
        self::assertSame(['a', 'b', 'c'], $queue->toArray());

        //FO
        $queue = queue(range('a', 'd'));
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $char = $queue->dequeue();
        self::assertSame('a', $char);
        self::assertCount(3, $queue);
        self::assertSame(['b', 'c', 'd'], $queue->toArray());

        $queue = queue(range('a', 'd'), Queue::DIRECTION_FIFO);
        self::assertCount(4, $queue);
        self::assertSame(['a', 'b', 'c', 'd'], $queue->toArray());
        $char = $queue->dequeue();
        self::assertSame('a', $char);
        self::assertCount(3, $queue);
        self::assertSame(['b', 'c', 'd'], $queue->toArray());
    }

    /**
     * @covers \Tale\queue
     * @covers ::__construct
     * @covers ::dequeue
     * @expectedException \OutOfRangeException
     */
    public function testDequeueThrowsExceptionWhenNoItemsLeft(): void
    {
        $queue = queue(['a']);
        self::assertSame('a', $queue->dequeue());
        $queue->dequeue();
    }

    private function generate(array $values): \Generator
    {
        yield from $values;
    }
}
