<?php
// File: src/Node.php

declare(strict_types=1);

namespace SortedList;

/**
 * Node class representing a single element in the linked list
 * @internal
 */
final readonly class Node
{
    public function __construct(
        public string|int $value,
        public ?self $next = null,
    ) {}
}