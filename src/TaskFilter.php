<?php

declare(strict_types=1);

namespace MiniAgentLab;

final class TaskFilter
{
    public const ALL = 'all';

    public const ACTIVE = 'active';

    public const COMPLETED = 'completed';

    private const FILTERS = [
        self::ALL,
        self::ACTIVE,
        self::COMPLETED,
    ];

    public static function normalize(mixed $filter): string
    {
        if (!is_string($filter)) {
            return self::ALL;
        }

        if (in_array($filter, self::FILTERS, true)) {
            return $filter;
        }

        return self::ALL;
    }

    public static function apply(array $tasks, string $filter): array
    {
        $filter = self::normalize($filter);

        if ($filter === self::ALL) {
            return $tasks;
        }

        return array_values(
            array_filter(
                $tasks,
                static fn (array $task): bool => $task['completed'] === ($filter === self::COMPLETED)
            )
        );
    }
}
