<?php

declare(strict_types=1);

namespace SortedList\Tests;

use PHPUnit\Framework\TestCase;
use SortedList\SortedLinkedList;

final class SortedLinkTest extends TestCase
{
    public function testConstructorWithEmptyList(): void
    {
        $list = new SortedLinkedList();

        $this->assertTrue($list->isEmpty());
        $this->assertSame(0, $list->count());
    }

    public function testConstructorWithIntegerArray(): void
    {
        $list = new SortedLinkedList([5, 2, 8, 1, 9]);

        $this->assertFalse($list->isEmpty());
        $this->assertSame(5, $list->count());
        $this->assertSame([1, 2, 5, 8, 9], $list->toArray());
    }

    public function testConstructorWithStringArray(): void
    {
        $list = new SortedLinkedList(['dog', 'cat', 'bird']);

        $this->assertSame(['bird', 'cat', 'dog'], $list->toArray());
    }

    public function testAddMaintainsSortOrder(): void
    {
        $list = new SortedLinkedList();

        $list->add(5)->add(2)->add(8)->add(1);

        $this->assertSame([1, 2, 5, 8], $list->toArray());
    }

    public function testAddDuplicates(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $list->add(2)->add(2);

        $this->assertSame([1, 2, 2, 2, 3], $list->toArray());
    }

    public function testAddThrowsExceptionOnTypeMismatch(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Cannot mix types');

        $list->add('string');
    }

    public function testRemoveExistingValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3, 4, 5]);

        $result = $list->remove(3);

        $this->assertTrue($result);
        $this->assertSame([1, 2, 4, 5], $list->toArray());
        $this->assertSame(4, $list->count());
    }

    public function testRemoveNonExistingValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $result = $list->remove(10);

        $this->assertFalse($result);
        $this->assertSame([1, 2, 3], $list->toArray());
    }

    public function testRemoveFromEmptyList(): void
    {
        $list = new SortedLinkedList();

        $result = $list->remove(1);

        $this->assertFalse($result);
    }

    public function testRemoveFirstValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $list->remove(1);

        $this->assertSame([2, 3], $list->toArray());
    }

    public function testRemoveLastValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $list->remove(3);

        $this->assertSame([1, 2], $list->toArray());
    }

    public function testRemoveOnlyRemovesFirstOccurrence(): void
    {
        $list = new SortedLinkedList([1, 2, 2, 2, 3]);

        $list->remove(2);

        $this->assertSame([1, 2, 2, 3], $list->toArray());
    }

    public function testContainsExistingValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3, 4, 5]);

        $this->assertTrue($list->contains(3));
        $this->assertTrue($list->contains(1));
        $this->assertTrue($list->contains(5));
    }

    public function testContainsNonExistingValue(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $this->assertFalse($list->contains(10));
        $this->assertFalse($list->contains(0));
    }

    public function testContainsOptimizedSearch(): void
    {
        $list = new SortedLinkedList([1, 2, 3, 4, 5, 6, 7, 8, 9]);

        // Should stop early when past the value
        $this->assertFalse($list->contains(0));
    }

    public function testGet(): void
    {
        $list = new SortedLinkedList([5, 2, 8]);

        $this->assertSame(2, $list->get(0));
        $this->assertSame(5, $list->get(1));
        $this->assertSame(8, $list->get(2));
    }

    public function testGetThrowsExceptionForNegativeIndex(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $this->expectException(\OutOfRangeException::class);

        $list->get(-1);
    }

    public function testGetThrowsExceptionForOutOfBoundsIndex(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $this->expectException(\OutOfRangeException::class);

        $list->get(10);
    }

    public function testFirst(): void
    {
        $list = new SortedLinkedList([5, 2, 8, 1]);

        $this->assertSame(1, $list->first());
    }

    public function testFirstThrowsExceptionOnEmptyList(): void
    {
        $list = new SortedLinkedList();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('List is empty');

        $list->first();
    }

    public function testLast(): void
    {
        $list = new SortedLinkedList([5, 2, 8, 1]);

        $this->assertSame(8, $list->last());
    }

    public function testLastThrowsExceptionOnEmptyList(): void
    {
        $list = new SortedLinkedList();

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('List is empty');

        $list->last();
    }

    public function testRemoveFirst(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $value = $list->removeFirst();

        $this->assertSame(1, $value);
        $this->assertSame([2, 3], $list->toArray());
        $this->assertSame(2, $list->count());
    }

    public function testRemoveFirstThrowsExceptionOnEmptyList(): void
    {
        $list = new SortedLinkedList();

        $this->expectException(\RuntimeException::class);

        $list->removeFirst();
    }

    public function testClear(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $result = $list->clear();

        $this->assertSame($list, $result);
        $this->assertTrue($list->isEmpty());
        $this->assertSame(0, $list->count());
    }

    public function testClearAllowsDifferentTypeAfter(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);
        $list->clear();

        $list->add('string');

        $this->assertSame(['string'], $list->toArray());
    }

    public function testIteration(): void
    {
        $list = new SortedLinkedList([3, 1, 2]);
        $values = [];

        foreach ($list as $value) {
            $values[] = $value;
        }

        $this->assertSame([1, 2, 3], $values);
    }

    public function testIterationOnEmptyList(): void
    {
        $list = new SortedLinkedList();
        $values = [];

        foreach ($list as $value) {
            $values[] = $value;
        }

        $this->assertSame([], $values);
    }

    public function testJsonSerialize(): void
    {
        $list = new SortedLinkedList([3, 1, 2]);

        $json = json_encode($list);

        $this->assertSame('[1,2,3]', $json);
    }

    public function testJsonSerializeEmptyList(): void
    {
        $list = new SortedLinkedList();

        $json = json_encode($list);

        $this->assertSame('[]', $json);
    }

    public function testFromArray(): void
    {
        $list = SortedLinkedList::fromArray([5, 2, 8]);

        $this->assertSame([2, 5, 8], $list->toArray());
    }

    public function testFromEmptyArray(): void
    {
        $list = SortedLinkedList::fromArray([]);

        $this->assertTrue($list->isEmpty());
    }

    public function testMerge(): void
    {
        $list1 = new SortedLinkedList([1, 3, 5]);
        $list2 = new SortedLinkedList([2, 4, 6]);

        $merged = $list1->merge($list2);

        $this->assertSame([1, 2, 3, 4, 5, 6], $merged->toArray());
        // Original lists unchanged
        $this->assertSame([1, 3, 5], $list1->toArray());
        $this->assertSame([2, 4, 6], $list2->toArray());
    }

    public function testMergeWithEmptyList(): void
    {
        $list1 = new SortedLinkedList([1, 2, 3]);
        $list2 = new SortedLinkedList();

        $merged = $list1->merge($list2);

        $this->assertSame([1, 2, 3], $merged->toArray());
    }

    public function testMergeWithDuplicates(): void
    {
        $list1 = new SortedLinkedList([1, 2, 3]);
        $list2 = new SortedLinkedList([2, 3, 4]);

        $merged = $list1->merge($list2);

        $this->assertSame([1, 2, 2, 3, 3, 4], $merged->toArray());
    }

    public function testFilter(): void
    {
        $list = new SortedLinkedList([1, 2, 3, 4, 5, 6]);

        $filtered = $list->filter(fn($x) => $x > 3);

        $this->assertSame([4, 5, 6], $filtered->toArray());
        // Original list unchanged
        $this->assertSame([1, 2, 3, 4, 5, 6], $list->toArray());
    }

    public function testFilterNoneMatch(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $filtered = $list->filter(fn($x) => $x > 10);

        $this->assertTrue($filtered->isEmpty());
    }

    public function testFilterAllMatch(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $filtered = $list->filter(fn($x) => $x > 0);

        $this->assertSame([1, 2, 3], $filtered->toArray());
    }

    public function testToString(): void
    {
        $intList = new SortedLinkedList([3, 1, 2]);
        $this->assertSame('[1, 2, 3]', (string)$intList);

        $strList = new SortedLinkedList(['cat', 'dog', 'bird']);
        $this->assertSame('["bird", "cat", "dog"]', (string)$strList);
    }

    public function testToStringEmptyList(): void
    {
        $list = new SortedLinkedList();
        $this->assertSame('[]', (string)$list);
    }

    public function testStringListSorting(): void
    {
        $list = new SortedLinkedList(['zebra', 'apple', 'mango', 'banana']);

        $this->assertSame(['apple', 'banana', 'mango', 'zebra'], $list->toArray());
    }

    public function testNegativeNumbers(): void
    {
        $list = new SortedLinkedList([5, -3, 0, -10, 7]);

        $this->assertSame([-10, -3, 0, 5, 7], $list->toArray());
    }

    public function testLargeList(): void
    {
        $values = range(100, 1);
        $list = new SortedLinkedList($values);

        $this->assertSame(100, $list->count());
        $this->assertSame(1, $list->first());
        $this->assertSame(100, $list->last());
    }

    public function testChaining(): void
    {
        $list = new SortedLinkedList();

        $result = $list->add(5)->add(2)->add(8)->clear()->add(1);

        $this->assertSame($list, $result);
        $this->assertSame([1], $list->toArray());
    }

    public function testAddToBeginning(): void
    {
        $list = new SortedLinkedList([2, 3, 4]);

        $list->add(1);

        $this->assertSame([1, 2, 3, 4], $list->toArray());
    }

    public function testAddToEnd(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $list->add(4);

        $this->assertSame([1, 2, 3, 4], $list->toArray());
    }

    public function testAddToMiddle(): void
    {
        $list = new SortedLinkedList([1, 3, 5]);
        $list->add(2)->add(4);
        $this->assertSame([1, 2, 3, 4, 5], $list->toArray());
    }

    public function testSingleElement(): void
    {
        $list = new SortedLinkedList([42]);

        $this->assertSame(1, $list->count());
        $this->assertSame(42, $list->first());
        $this->assertSame(42, $list->last());
        $this->assertSame(42, $list->get(0));
        $this->assertTrue($list->contains(42));
    }

    public function testRemoveSingleElement(): void
    {
        $list = new SortedLinkedList([42]);

        $list->remove(42);

        $this->assertTrue($list->isEmpty());
    }

    public function testStringCaseInsensitivity(): void
    {
        // Note: default sorting is case-sensitive
        $list = new SortedLinkedList(['apple', 'Banana', 'cherry']);

        // Capital letters come before lowercase in ASCII
        $this->assertSame(['Banana', 'apple', 'cherry'], $list->toArray());
    }

    public function testCountAfterOperations(): void
    {
        $list = new SortedLinkedList([1, 2, 3]);

        $this->assertSame(3, $list->count());

        $list->add(4);
        $this->assertSame(4, $list->count());

        $list->remove(2);
        $this->assertSame(3, $list->count());

        $list->removeFirst();
        $this->assertSame(2, $list->count());

        $list->clear();
        $this->assertSame(0, $list->count());
    }
}