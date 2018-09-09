<?php
declare(strict_types=1);

namespace Tale\Benchmark;

use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;
use Tale\Collection;

/**
 * @Warmup(1)
 * @Revs(2)
 * @Iterations(64)
 */
class CollectionBench
{
    private $mapper;
    private $filterer;

    public function __construct()
    {
        $this->mapper = function (string $value) {
            return strtoupper($value);
        };
        $this->filterer = function (string $value) {
            return $value !== 'A' && $value !== 'B';
        };
    }

    public function benchNativeFunctionProcessing(): void
    {
        $mapper = $this->mapper;
        $filterer = $this->filterer;
        $data = iterator_to_array($this->generate());
        $data = array_map($mapper, $data);
        $data = array_filter($data, $filterer);
        $values = array_values($data);
    }

    public function benchNativeLoopProcessing(): void
    {
        $mapper = $this->mapper;
        $filterer = $this->filterer;
        $values = [];
        foreach ($this->generate() as $value) {
            $value = $mapper($value);
            if ($filterer($value)) {
                $values[] = $value;
            }
        }
    }

    public function benchCollectionProcessing(): void
    {
        $mapper = $this->mapper;
        $filterer = $this->filterer;
        $values = (new Collection($this->generate()))
            ->map($mapper)
            ->filter($filterer)
            ->getValues()
            ->toArray();
    }

    private function generate(): \Generator
    {
        for ($i = 0; $i < 10; $i++) {
            yield from range('a', 'z');
        }
    }
}