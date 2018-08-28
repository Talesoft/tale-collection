
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

### Set
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

### Map
```php
use function Tale\map;

$key1 = new Key1();
$key2 = new Key2();

$map = map();
$map->set($key1, 'value 1');
$map->set($key2, 'value 2');
    
var_dump($set->toArray());
/*
array(3) {
  0 => object(SomeOtherClass)#1 (0) {}
  1 => object(SomeClass)#2 (0) {}
}
*/
```


TODO: More docs.