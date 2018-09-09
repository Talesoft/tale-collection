
[![Packagist](https://img.shields.io/packagist/v/talesoft/tale-collection.svg?style=for-the-badge)](https://packagist.org/packages/talesoft/tale-collection)
[![License](https://img.shields.io/github/license/Talesoft/tale-collection.svg?style=for-the-badge)](https://github.com/Talesoft/tale-collection/blob/master/LICENSE.md)
[![CI](https://img.shields.io/travis/Talesoft/tale-collection.svg?style=for-the-badge)](https://travis-ci.org/Talesoft/tale-collection)
[![Coverage](https://img.shields.io/codeclimate/coverage/Talesoft/tale-collection.svg?style=for-the-badge)](https://codeclimate.com/github/Talesoft/tale-collection)

Tale Collection
===============

What is Tale Collection?
------------------------

Tale Collection is a heavily iterator-based Collection implementation for PHP.

It comes with a generic Collection class that handles chained iteration
well as well as a Map and Set implementation with a solid API.

Installation
------------

```bash
composer require talesoft/tale-collection
```

Usage
-----

### Collection

#### Signature

```php
Tale\collection(iterable $iterable): Tale\CollectionInterface
//or
new Tale\Collection(iterable $iterable)
```

#### Examples

```php
use function Tale\collection;

$collection = collection(['a', 'b', 'c', 'd']))
    ->filter(function (string $char) {
        return $char !== 'b';
    })
    ->map(function (string $char) {
        return strtoupper($char)
    })
    ->flip();
    
var_dump($collection['c']); //int(2)
var_dump($collection->toArray());
/*
array(3) {
  'a' => int(0)
  'c' => int(2)
  'd' => int(3)
}
*/
```

As stated, it's heavily iterator-based, so many stacked operations 
won't ever turn anything into an actual array unless you actually
access the data. 

Let's demonstrate!

```php
use function Tale\collection;

funuction generateAtoZ()
{
    //Imagine this is some hard work
    yield from range('a', 'z');
}

$collection = collection(generateAtoZ())
    //We filter all a-s
    ->filter(function (string $char) {
        return $char !== 'a';
    })
    //We also chain a normal SPL RegexIterator and drop all i to z-s
    ->chain(\RegexIterator::class, '/[^i-z]/')
    //Uppercase all characters
    ->map(function (string $char) {
        return strtoupper($char);
    })
    //Flip the keys and values
    ->flip()
    //Drop characters with the index 3 (this will be 'C')
    ->filter(function (int $index) {
        return $index !== 2;
    })
    //Flip again
    ->flip()
    //Only yield the values (array_values, basically)
    ->getValues();
    
//This right below is the _first_ part where generateAtoZ will
//actually be triggered at all! Until then we basically did nothing
//except for chaining some iterators

var_dump($collection[2]); //string(1) "E"

//Notice how it's E, because we dropped A and C

var_dump($collection->toArray());
/*
array(6) {
  [0] => string(1) "B"
  [1] => string(1) "D"
  [2] => string(1) "E"
  [3] => string(1) "F"
  [4] => string(1) "G"
  [5] => string(1) "H"
}
*/
```

**Most** methods of Tale collections return collections again. Collections don't only contain 
arrays, but can also contain an iterable. The iterable will be passed through most methods you
work with and will only be lazy-triggered when you access keys or cast it to an array.

This gives the possibility to deeply modify iterable values without creating array copies all over.

In fact, **not a single** of the methods called in the example above except for `toAray()` ever
triggered the `generateAtoZ()` generator, not even `getValues()` or `flip()`. They all return collections 
containing a nested specific iterator.

This also leads to some nice side-effects, like `flip()` not overwriting
values.

Imagine we want to do something with the keys of an array without
overwriting its values when
flipping (very memory efficiently):

```php
use function Tale\collection;

$values = collection(['a' => 2, 'b' => 2, 'c' => 3])
    ->flip()
    ->map(function (string $key) {
        return strtoupper($key);
    })
    ->flip();
    
var_dump($values->toArray());
/*
array(3) {
  'A' => int(2)
  'B' => int(2)
  'C' => int(3)
}
*/
```

To understand what happened, let's look at the native PHP equivalent

```php
use function Tale\collection;

$values = array_flip(['a' => 2, 'b' => 2, 'c' => 3]);
//Here our values are lost already, it will result in
// [2 => 'b', 3 => 'c'], as keys are unique

$values = array_map(function (string $key) {
    return strtoupper($key);
}, $values);

$values = array_flip(['a' => 2, 'b' => 2, 'c' => 3]);

var_dump($values);
/*
array(2) {
  ["B"]=> int(2)
  ["C"]=> int(3)
}
*/
```

Through the way iterators work, the values are simply never reduced
to an actual array, only when retrieving the end-result. Duplicate
keys are no problem during iteration and will always stay available
until the iterable gets casted to an array internally.

If you're not much into iterators yet, I've prepared a little comparison
that explains their advantage in detail.

Check out [Iterators vs Arrays](https://github.com/Talesoft/tale-collection/blob/master/docs/iterators-vs-arrays.md)!

Specialized collections
-----------------------

All of the following collections use the same iterator mechanisms
as the collection class discussed above. In fact,
there is the `Tale\CollectionInterface` they all implement and all of
them use a common base class `Tale\AbstractCollection` that defines
most of their APIs.

### Set

The Set class is an abstraction for a set of values where keys
don't matter. The keys of a set will be managed and always stay 
sequential.

The values will be unique. If a value already exists in the set, 
it won't be added again when using `add`.

The content is managed via the following API:

##### Set->has($item)
Checks if the set contains a specific item

##### Set->add($item)
Adds a new item to the set

##### Set->remove($item)
Removes an item from the set

```php
use function Tale\set;

$existingObject = new SomeClass();

$set = set();
$set->add(new SomeOtherClass());

var_dump($set->has($existingObject)); //bool(true)

var_dump($set->toArray());
/*
array(3) {
  0 => object(SomeOtherClass)#1 (0) {}
  1 => object(SomeClass)#2 (0) {}
}
*/
```

#### Common use-cases

**A set of CSS classes on elements**
```php
use function Tale\set;

$classList = set(['btn']);

if ($primary) {
    $classList->add('btn-primary');
}

if ($block) {
    $classList->add('btn-block');
}

if ($classList->has('primary') {
    echo "This is a primary button!\n";
}

echo "<button class=\"{$classList->join(' ')}\">My button!</button>";

```

### Map

The map is a data structure that allows using keys other than 
strings and integers. Through limitations of the PHP engine, 
you can't access complex keys via array access (`[$obj]`).

The following API manages a map:

##### Map->get($key)
Retrieves the value for a specific key

##### Map->set($key, $value)
Sets the value for a specific key

##### Map->has($key)
Checks whether a specific key exists or not

##### Map->remove($key)
Removes a value with a specific key from the map

```php
use function Tale\map;

$key1 = new Key1();
$key2 = new Key2();

$map = map();
$map->set($key1, 'value 1');
$map->set($key2, 'value 2');
    
var_dump($map->toArray());
/*
array(3) {
  0 => array(2) {
    0 => object(SomeOtherClass)#1 (0) {}
    1 => string(7) "value 1"
  }
  1 => array(2) {
    0 => object(SomeClass)#2 (0) {}
    1 => string(7) "value 2"
  }
}
*/
```

#### Common use-cases

**A metadata storage for objects**
```php
use function Tale\map;

$metadata = map();

$b = new B();

$objects = [
    new A(),
    $b,
    new C(),
    new D()
];

foreach ($objects as $obj) {
    $metadata->set($obj, getMetadataForObject($obj));
}

//[...]

if ($metadata->has($b)) {
    $bMetadata = $metadata->get($b);
    
    //$bMetadata is now the metadata for the $b instance
}
```

TODO: More docs.