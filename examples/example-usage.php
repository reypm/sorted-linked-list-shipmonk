<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use SortedList\SortedLinkedList;

echo "=== SortedLinkedList Examples ===\n\n";

// Example 1: Integer list
echo "1. Integer List\n";
$numbers = new SortedLinkedList([5, 2, 8, 1, 9, 3]);
echo "Initial list: {$numbers}\n";
echo "Count: {$numbers->count()}\n";
echo "First: {$numbers->first()}, Last: {$numbers->last()}\n";
echo "Contains 5: " . ($numbers->contains(5) ? 'yes' : 'no') . "\n\n";

// Example 2: Add and remove
echo "2. Add and Remove Operations\n";
$numbers->add(4)->add(6);
echo "After adding 4 and 6: {$numbers}\n";
$numbers->remove(8);
echo "After removing 8: {$numbers}\n\n";

// Example 3: String list
echo "3. String List\n";
$animals = new SortedLinkedList(['dog', 'cat', 'bird', 'elephant', 'ant']);
echo "Animals: {$animals}\n";
echo "Alphabetically first: {$animals->first()}\n";
echo "Alphabetically last: {$animals->last()}\n\n";

// Example 4: Iteration
echo "4. Iteration\n";
echo "Iterating through animals: ";
foreach ($animals as $animal) {
    echo "{$animal} ";
}
echo "\n\n";

// Example 5: Filtering
echo "5. Filtering\n";
$filtered = $numbers->filter(fn($x) => $x > 4);
echo "Numbers greater than 4: {$filtered}\n\n";

// Example 6: Merging
echo "6. Merging Lists\n";
$list1 = new SortedLinkedList([1, 3, 5]);
$list2 = new SortedLinkedList([2, 4, 6]);
$merged = $list1->merge($list2);
echo "List 1: {$list1}\n";
echo "List 2: {$list2}\n";
echo "Merged: {$merged}\n\n";

// Example 7: JSON serialization
echo "7. JSON Serialization\n";
$data = new SortedLinkedList([100, 50, 75, 25]);
echo "JSON: " . json_encode($data) . "\n\n";

// Example 8: Array conversion
echo "8. Array Conversion\n";
$array = [9, 7, 5, 3, 1];
$list = SortedLinkedList::fromArray($array);
echo "From array {" . implode(', ', $array) . "}: {$list}\n";
echo "To array: {" . implode(', ', $list->toArray()) . "}\n\n";

// Example 9: Type safety
echo "9. Type Safety\n";
try {
    $typed = new SortedLinkedList([1, 2, 3]);
    echo "Created integer list: {$typed}\n";
    $typed->add("string"); // This will throw an exception
} catch (InvalidArgumentException $e) {
    echo "Error (expected): {$e->getMessage()}\n\n";
}

// Example 10: Empty list operations
echo "10. Empty List Handling\n";
$empty = new SortedLinkedList();
echo "Is empty: " . ($empty->isEmpty() ? 'yes' : 'no') . "\n";
try {
    $empty->first();
} catch (RuntimeException $e) {
    echo "Error accessing empty list (expected): {$e->getMessage()}\n";
}

echo "\n=== All examples completed! ===\n";