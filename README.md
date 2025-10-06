# SortedLinkedList

A type-safe, sorted linked list implementation for PHP 8.4+ that automatically maintains elements in ascending order.

[![PHP Version](https://img.shields.io/badge/php-%5E8.4-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## Features

- 🔒 **Type-safe**: Supports `string` or `int` values (but not mixed)
- 🔄 **Auto-sorted**: Maintains ascending order automatically
- 🎯 **Modern PHP**: Built with PHP 8.4+ features
- 🔧 **Fluent API**: Method chaining support
- 📦 **Standard interfaces**: Implements `IteratorAggregate`, `Countable`, `JsonSerializable`
- 🧪 **Well-tested**: Comprehensive test coverage
- 📝 **Fully documented**: PHPDoc with generics support

## Installation

```bash
composer require reypm/sorted-linked-list
```

## Requirements

- PHP 8.4 or higher

## Usage

### Basic Operations

```php
use SortedList\SortedLinkedList;

// Create from values
$list = new SortedLinkedList([5, 2, 8, 1, 9]);
echo $list; // [1, 2, 5, 8, 9]

// Add values (maintains sort order)
$list->add(4)->add(6);
echo $list; // [1, 2, 4, 5, 6, 8, 9]

// Check contents
$list->contains(5); // true
$list->isEmpty();   // false
$list->count();     // 7

// Access elements
$list->first(); // 1 (minimum)
$list->last();  // 9 (maximum)
$list->get(3);  // 5 (by index)

// Remove elements
$list->remove(8);     // Remove value
$list->removeFirst(); // Remove and return first
$list->clear();       // Remove all
```

### String Lists

```php
$animals = new SortedLinkedList(['dog', 'cat', 'bird', 'elephant']);
echo $animals; // ["bird", "cat", "dog", "elephant"]
```

### Iteration

```php
foreach ($list as $value) {
    echo $value . "\n";
}
```

### Functional Operations

```php
// Filter
$filtered = $list->filter(fn($x) => $x > 5);

// Merge two lists
$list1 = new SortedLinkedList([1, 3, 5]);
$list2 = new SortedLinkedList([2, 4, 6]);
$merged = $list1->merge($list2); // [1, 2, 3, 4, 5, 6]

// Convert to array
$array = $list->toArray();

// Create from array
$list = SortedLinkedList::fromArray([3, 1, 2]);
```

### JSON Serialization

```php
$list = new SortedLinkedList([3, 1, 2]);
echo json_encode($list); // [1,2,3]
```

## Type Safety

The list enforces type consistency - once you add a value, only that type is allowed:

```php
$list = new SortedLinkedList([1, 2, 3]);
$list->add("string"); // Throws InvalidArgumentException
```

## API Reference

### Constructor
- `__construct(iterable $items = [])` - Create new list with optional initial items

### Modification Methods
- `add(string|int $value): self` - Add value in sorted position
- `remove(string|int $value): bool` - Remove first occurrence
- `removeFirst(): string|int` - Remove and return first value
- `clear(): self` - Remove all elements

### Query Methods
- `contains(string|int $value): bool` - Check if value exists
- `get(int $index): string|int` - Get value by index
- `first(): string|int` - Get minimum value
- `last(): string|int` - Get maximum value
- `isEmpty(): bool` - Check if empty
- `count(): int` - Get number of elements

### Transformation Methods
- `filter(callable $predicate): self` - Filter by predicate
- `merge(SortedLinkedList $other): self` - Merge with another list
- `toArray(): array` - Convert to array
- `static fromArray(array $items): self` - Create from array

## Development

### Running Tests

```bash
composer test
```

### Code Quality

```bash
# Static analysis
composer phpstan

# Code style check
composer cs-check

# Fix code style
composer cs-fix
```

## License

MIT License - see [LICENSE](LICENSE) file for details

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Author

Reynier Perez - reynierpm@gmail.com