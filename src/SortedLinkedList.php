<?php
declare(strict_types=1);

namespace SortedList;

use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use JsonSerializable;
use OutOfRangeException;
use RuntimeException;
use Traversable;

/**
 * Type-safe sorted linked list that maintains elements in ascending order
 * @template T of string|int
 * @implements IteratorAggregate<int, string|int>
 */
final class SortedLinkedList implements IteratorAggregate, Countable, JsonSerializable
{
    /** @var Node|null */
    private ?Node $head = null;
    private int $size = 0;
    private ?string $type = null;

    /**
     * Create a new sorted linked list
     * @param iterable<string|int> $items Optional initial items
     */
    public function __construct(iterable $items = [])
    {
        foreach ($items as $item) {
            $this->add($item);
        }
    }

    /**
     * Add a value to the list in sorted order
     * @param string|int $value
     * @return $this
     * @throws InvalidArgumentException
     */
    public function add(string|int $value): self
    {
        $this->validateType($value);

        // Empty list or insert at beginning
        if ($this->head === null || $this->compare($value, $this->head->value) <= 0) {
            $this->head = new Node($value, $this->head);
            $this->size++;
            return $this;
        }

        // Find insertion point and rebuild chain
        $values = [];
        $inserted = false;
        $current = $this->head;

        while ($current !== null) {
            if (!$inserted && $this->compare($value, $current->value) <= 0) {
                $values[] = $value;
                $inserted = true;
            }
            $values[] = $current->value;
            $current = $current->next;
        }

        if (!$inserted) {
            $values[] = $value;
        }

        $this->rebuildFromArray($values);
        $this->size++;

        return $this;
    }

    /**
     * Remove first occurrence of a value
     */
    public function remove(string|int $value): bool
    {
        if ($this->head === null) {
            return false;
        }

        if ($this->head->value === $value) {
            $this->head = $this->head->next;
            $this->size--;
            return true;
        }

        $values = [];
        $removed = false;
        $current = $this->head;

        while ($current !== null) {
            if (!$removed && $current->value === $value) {
                $removed = true;
            } else {
                $values[] = $current->value;
            }
            $current = $current->next;
        }

        if ($removed) {
            $this->rebuildFromArray($values);
            $this->size--;
            return true;
        }

        return false;
    }

    /**
     * Check if the list contains a value
     * @param string|int $value
     * @return bool
     */
    public function contains(string|int $value): bool
    {
        $current = $this->head;
        while ($current !== null) {
            $cmp = $this->compare($current->value, $value);
            if ($cmp === 0) {
                return true;
            }
            if ($cmp > 0) {
                return false;
            }
            $current = $current->next;
        }
        return false;
    }

    /**
     * Get value at index
     * @param int $index
     * @return string|int
     * @throws OutOfRangeException
     */
    public function get(int $index): string|int
    {
        if ($index < 0 || $index >= $this->size) {
            throw new OutOfRangeException("Index $index out of range");
        }

        $current = $this->head;
        for ($i = 0; $i < $index; $i++) {
            $current = $current->next;
        }

        return $current->value;
    }

    /**
     * Get the first (minimum) value
     * @return string|int
     * @throws RuntimeException
     */
    public function first(): string|int
    {
        if ($this->head === null) {
            throw new RuntimeException("List is empty");
        }
        return $this->head->value;
    }

    /**
     * Get the last (maximum) value
     * @return string|int
     * @throws RuntimeException
     */
    public function last(): string|int
    {
        if ($this->head === null) {
            throw new RuntimeException("List is empty");
        }

        $current = $this->head;
        while ($current->next !== null) {
            $current = $current->next;
        }
        return $current->value;
    }

    /**
     * Remove and return the first value
     * @return string|int
     * @throws RuntimeException
     */
    public function removeFirst(): string|int
    {
        if ($this->head === null) {
            throw new RuntimeException("List is empty");
        }

        $value = $this->head->value;
        $this->head = $this->head->next;
        $this->size--;
        return $value;
    }

    /**
     * Clear all elements
     * @return self<T>
     */
    public function clear(): self
    {
        $this->head = null;
        $this->size = 0;
        $this->type = null;
        return $this;
    }

    /**
     * Check if list is empty
     */
    public function isEmpty(): bool
    {
        return $this->head === null;
    }

    /**
     * Get number of elements
     */
    public function count(): int
    {
        return $this->size;
    }

    /**
     * Convert to array
     * @return array<int, string|int>
     */
    public function toArray(): array
    {
        $result = [];
        $current = $this->head;
        while ($current !== null) {
            $result[] = $current->value;
            $current = $current->next;
        }
        return $result;
    }

    /**
     * Get iterator for foreach support
     * @return Traversable<int, string|int>
     */
    public function getIterator(): Traversable
    {
        $current = $this->head;
        while ($current !== null) {
            yield $current->value;
            $current = $current->next;
        }
    }

    /**
     * JSON serialization
     * @return array<int, string|int>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create a new list from array
     * @param array<string|int> $items
     * @return self
     */
    public static function fromArray(array $items): self
    {
        return new self($items);
    }

    /**
     * Merge with another sorted list
     * @param self $other
     * @return self
     */
    public function merge(self $other): self
    {
        $result = new self();
        foreach ($this as $value) {
            $result->add($value);
        }
        foreach ($other as $value) {
            $result->add($value);
        }
        return $result;
    }

    /**
     * Filter elements by predicate
     * @param callable(string|int): bool $predicate
     * @return self
     */
    public function filter(callable $predicate): self
    {
        $result = new self();
        foreach ($this as $value) {
            if ($predicate($value)) {
                $result->add($value);
            }
        }
        return $result;
    }

    /**
     * Get string representation
     */
    public function __toString(): string
    {
        return '[' . implode(', ', array_map(
                fn($v) => is_string($v) ? "\"$v\"" : (string)$v,
                $this->toArray()
            )) . ']';
    }

    /**
     * Validate type consistency
     * @param string|int $value
     * @throws InvalidArgumentException
     */
    private function validateType(string|int $value): void
    {
        $valueType = get_debug_type($value);

        if ($this->type === null) {
            if ($valueType !== 'string' && $valueType !== 'int') {
                throw new InvalidArgumentException(
                    "Only string or int values are allowed, $valueType given"
                );
            }
            $this->type = $valueType;
        } elseif ($valueType !== $this->type) {
            throw new InvalidArgumentException(
                "Cannot mix types: list contains $this->type, attempting to add $valueType"
            );
        }
    }

    /**
     * Compare two values
     * @param string|int $a
     * @param string|int $b
     * @return int
     */
    private function compare(string|int $a, string|int $b): int
    {
        return $a <=> $b;
    }

    /**
     * Rebuild the linked list from an array of values
     * @param array<string|int> $values
     * @return void
     */
    private function rebuildFromArray(array $values): void
    {
        $this->head = null;

        if (count($values) === 0) {
            return;
        }

        // Build chain backwards from last to first
        for ($i = count($values) - 1; $i >= 0; $i--) {
            $this->head = new Node($values[$i], $this->head);
        }
    }
}