Iterators vs. Arrays
--------------------

Iterators won't ever replace arrays for you. Most of the time,
you will be iterating arrays with an iterator.

The main difference between using iterators and normal array operations
lies in **memory**.

To understand why, let's have a look at a common array operation
done with native array functions and iterators

Suppose you have an array that looks like this:
```php
$array = ['a', 'b', 'c', 'd'];
```

Let's give ourselves two tasks for this array:
- Remove all values that are 'c'
- Upper-case all values
- Format all values as 'Value: %s'

The resulting array should look like this:
```php
['Value: A', 'Value: B', 'Value: D']
```

Natively, there are two ways to do this:

**Approach 1:** PHP's array_* functions
```php
$filtered = array_filter($array, function ($value) {
    return $value !== 'c';
});

$mapped = array_map('strtoupper', $filtered);

$formatted = array_map(function ($value) {
    return sprintf('Value: %s', $value);
}, $mapped);
```

Apart from the parameter order that is a common problem for
many, this approach is often preferred because it is very **readable** 
and **extensible** (sometimes, depending on who you ask). Not much 
complexity is introduced and it's simple to re-use the functions passed 
and provide a common set of filters, mappers etc.
It's also easy to extend, as you can just chain another two lines of code
below or between.

The downside to this approach is memory usage, as every time
the array is passed to a function, a new copy of it will be created.
`$filtered`, `$mapped` and `$formatted` will all be new arrays
with new values sitting in memory at that point.

**Approach 2:** A single loop
```php
$values = [];

foreach ($array as $key => $value) {
    $values[$key] = sprintf('Value: %s', strtoupper($value));
}
```

This construct looks like the simplest on first glance
and it surely is the most memory efficient we can get.
Only a single copy of the values will be created in `$values`,
everything else will be thrown out of memory after each loop 
iteration.

The downside to this approach is **readability** and **extensibility**.
Imagine you want to add more processes to this, at some point you will end
up with many wrapped functions and copied values all over.
To extend it you have to keep track of all braces carefully and place
the follow-up functions between.
The more complex the processes become 
(keep in mind, `$value !== 'c'`, `strtoupper` and `sprintf('Value: %s', $value)`)
are among the most simple examples that can be provided for array tasks during iteration),
the harder to read and maintain this thing will become. As soon as you
start splitting it into multiple functions, you end up creating
array copies again.


## Iterators to the rescue!

Iterators are the best of both worlds, through the way they work internally.

Let's clear up terminology a bit, especially if you're new to iterators:


### What is an iterator?

An iterator in PHP is any class that implements the `\Iterator` interface.
It comes with a set of functions, namely `current()`, `key()`, `valid()`, `next()` and `rewind()`
that allow you to simulate an iteration over something. Anything.
Passing an iterator to a `foreach`-loop will make PHP call specific
methods on it in a specific order. 

To understand what I mean, take a look at the following.

These two constructs are **exactly the same** for PHP:
```php
$iterator = new SomeIterator(); //This implements \Iterator

foreach ($iterator as $key => $value) {
    echo "Key: {$key}, Value: {$value}\n";
}
```

```php
$iterator = new SomeIterator(); //This implements \Iterator

for ($iterator->rewind(); $iterator->valid(); $iterator->next()) {
    $key = $iterator->key();
    $value = $iterator->current();
    
    echo "Key: {$key}, Value: {$value}\n";
}
```

`foreach` is basically just a short way to iterate through an iterator.

### IteratorIterators

An IteratorIterator sounds messy, but it's really useful. It's a decorator
for existing iterator instances that simply forwards all calls to the inner iterator.

This gives us the possibility to intercept values and act on them.

As an example, let's implement a very basic map iterator that upper-cases all 
values that are iterated with it:

```php
class StrtoupperIterator extends \IteratorIterator
{
    public function current()
    {
        return strtoupper(parent::current());
    }
}
```

after that we can feed any iterator to it and it will upper-case all values.
Generators are iterators, so let's use that (and with it, also give a good example of
its power)

```php
function generate()
{
    yield 'hello';
    yield 'we';
    yield 'are';
    yield 'values';
}

$iterator = new StrtoupperIterator(generate());

foreach ($iterator as $value) {
    echo "{$value}\n";
}
```

The output of this would be the following:

```text
HELLO
WE
ARE
VALUES
```

#### How?

The value of each iteration step (`$value`) through PHP will be retrieved
by calling `current()` on `$iterator`. With out implementation, we made
the outer iterator (`StrtoupperIterator`) call `strtoupper` on all values
yielded by the inner iterator (Generator instance of `generate()`).

The actual values you end up with are uppercased.

This is the same as doing
```php
foreach (generate() as $value) {
    echo strtoupper($value)."\n";
}
```
in every regard, especially memory-wise. No copy of any array
will ever be created, values are piped through directly.

### ArrayIterator

Native PHP arrays are not objects and thus, they can't be `\Iterator` instances.
Even though, `foreach` can iterate arrays, it uses a different set of functions for them.

These two are the same:

```php
$array = ['a', 'b', 'c'];

foreach ($array as $key => $value) {
    echo "Key: {$key}, Value: {$value}\n";
}
```

```php
$array = ['a', 'b', 'c'];

for (reset($array); key($array) != null; next($array)) {
    $key = key($array);
    $value = current($array);
    echo "Key: {$key}, Value: {$value}\n";
}
```

When working with `IteratorIterator`s, you will notice that they
expect a `\Traversable` instance. This is because it forwards
all actual method calls (`current()`, `key()`, `valid()`, `next()`, `rewind()`)
and arrays don't have these methods. 

The following doesn't work:

```php
$iterator = new StrtoupperIterator(['hello', 'we', 'are', 'values']);
```

`\Traversable` doesn't have all the methods, either, but it can do 
`$iterator instanceof \IteratorAggregate ? $iterator->getIterator() : $iterator` to resolve it to a fully 
working `\Iterator` instance. I won't go into depth for `\IteratorAggregate` here, check out the PHP docs on that 
one if you're interested.

In order to turn a normal, native PHP array into a valid iterator, you
can use `ArrayIterator`.

```php
$iterator = new ArrayIterator(['hello', 'we', 'are', 'values']);
$uppercaseIterator = new StrtoupperIterator($iterator);
```

Luckily, the `ArrayIterator` will only create a single array copy in its constructor.
Iteration after that works without value copying.

Iterating `$uppercaseIterator` would look like this:

```php
foreach ($uppercaseIterator as $value) {
    echo "{$value}\n";
}
```

The output of this would be the following:

```text
HELLO
WE
ARE
VALUES
```

just like above.

### Different iterators

Utilizing `IteratorIterator` correctly, you can do a lot more than simply mapping
values. You can **skip** values, as an example:

```php
class AreFilterIterator extends \IteratorIterator
{
    public function valid()
    {
        while (($valid = parent::valid()) && $this->current() === 'are') {
            $this->next();
        }
        return $valid;
    }
}
```

This iterator would automatically skip all values that are exactly the string `"are"`.

It doesn't need to know and next or previous values for that, just the current value
and the instruction to skip it with `next()` when we want it. No array copies required.

Let's run it:



```php
$iterator = new ArrayIterator(['hello', 'we', 'are', 'values']);

$filterIterator = new AreFilterIterator($iterator);

foreach ($filterIterator as $value) {
    echo "{$value}\n";
}
```

The output of this would be the following:

```text
hello
we
values
```

The string `"are"` has been filtered out by calling `next()` when it occured.


### Stacking iterators

While this all might seem useless, the real fun starts when a certain realization kicks in:
You can put `IteratorIterator`s into `IteratorIterator`s, since they are
instances of `\Iterator` themself!

This allows us to stack iterators

```php
$iterator = new ArrayIterator(['hello', 'we', 'are', 'values']);

$filterIterator = new AreFilterIterator($iterator);

$uppercaseIterator = new StrtoupperIterator($filterIterator);

foreach ($uppercaseIterator as $value) {
    echo "{$value}\n";
}
```

The output of this would be the following:

```text
HELLO
WE
VALUES
```

Both happened, we filtered `"are"` strings and uppercased all values.
Whats most important, we never created a single copy of the initial array except
in the constructor of `ArrayIterator`.

**This is exactly the middle solution we've been looking for at the very start of this guide!**

Memory-wise, what's happening is exactly this:

```php
$array = ['hello', 'we', 'are', 'values'];

foreach ($array as $value) {
    if ($value === 'are') {
        continue;
    }
    echo strtoupper($value)."\n";
}
```

the only overhead being two class instances that only reference another instance
(very lightweight on memory) and a few method calls.

What's even more important, it's **readable and extensible**!

We have a clear structure of what's happening and we can swap
out implementations for different ones at all points or switch places:

```php
$iterator = new ArrayIterator(['HELLO', 'WE', 'ARE', 'VALUES']);

$lowercaseIterator = new StrtolowerIterator($iterator);

$filterIterator = new AreFilterIterator($lowercaseIterator);

foreach ($filterIterator as $value) {
    echo "{$value}\n";
}
```

Result:

```text
hello
we
values
```

It's also possible to declare a bunch of useful iterators and re-use them all over
your application where applicable.

The library [talesoft/tale-iterator](https://github.com/Talesoft/tale-iterator) does just that
and provides quite a few useful standard iterators that extend the existing
[PHP SPL Iterators](http://php.net/manual/de/spl.iterators.php), that are even more useful
and contain things like `RecursiveDirectoryIterator`, `GlobIterator` or `LimitIterator`.


**This is what this library does and uses.**


Let's have a look at a common example of using the `Tale\Collection`.


```php
use Tale\Collection;

$values = (new Collection(['hello', 'we', 'are', 'values']))
    ->chain(AreFilterIterator::class)
    ->chain(StrtoupperIterator::class)
    ->getValues()
    ->flip()
    ->filter(function (int $key) {
        return $key !== 0;
    })
    ->flip()
    ->map(function (string $value) {
        return "{$value}!";
    })
    ->forEach(function ($value) {
        echo "{$value}\n";
    });
```

Wow, this looks like a lot of array operations and probably a lot of array
copies all over, right? **Wrong!**


You might thing, memory-wise, the above is similar to this:

```php
$values = ['hello', 'we', 'are', 'values'];

$filteredValues = array_filter($values, function (string $value) {
    return $value !== 'are';
});

$uppercaseValues = array_map(function (string $value) {
    return strtoupper($value);
}, $filteredValues);

$values = array_values($uppercaseValues);

$flipped = array_flip($values);

$filteredValues = array_filter($flipped, function (int $key) {
    return $key !== 0;
});

$flipped = array_flip($filteredValues);

$mapped = array_map(function (string $value) {
    return "{$value}!";
}, $flipped);

foreach ($mapped as $value) {
    echo "{$value}\n";
}
```

creating array-copies all over and generally having a really high memory profile.

But I tell you: Through iterators and the way `Tale\Collection` uses them for many purposes,
memory-wise, what's actual going on looks more like this:


```php
use Tale\Collection;

$array = ['hello', 'we', 'are', 'values'];

foreach ($array as $key => $value) {
    if ($value === 'are') {
        continue;
    }
    $value = strtoupper($value);
    if ($key == 0) {
        continue;
    }
    $value = "{$value}!";
    echo "{$value}\n";
}
```

This looks way different and way more efficient, right? And it's actually the way it works.
`getValues()` basically does nothing, except for counting new keys that are
ignored for the most part. `flip()` uses `Tale\Iterator\FlipIterator` and the only
thing it does is yielding a key for a value and a value for a key, the only overhead
it has is a method call to `key()` and `current()`.

Especially when working with large, generated data sets, tale-collection will be really
lightweight for your memory and through less copying procedures needed, also a lot faster.


If you need any more explanations to the principles of this library,
feel free to ask me directly by emailing [torben@talesoft.codes](mailto:torben@talesoft.codes) anytime!
