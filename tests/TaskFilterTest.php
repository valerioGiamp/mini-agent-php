<?php

declare(strict_types=1);

use MiniAgentLab\TaskFilter;
use PHPUnit\Framework\TestCase;

final class TaskFilterTest extends TestCase
{
    public function testAllFilterReturnsEveryTask(): void
    {
        $tasks = $this->tasks();

        self::assertSame($tasks, TaskFilter::apply($tasks, 'all'));
    }

    public function testActiveFilterReturnsIncompleteTasks(): void
    {
        self::assertSame(
            [
                [
                    'id' => 'active-task',
                    'title' => 'Active task',
                    'priority' => 'medium',
                    'due_date' => null,
                    'description' => null,
                    'completed' => false,
                ],
            ],
            TaskFilter::apply($this->tasks(), 'active')
        );
    }

    public function testCompletedFilterReturnsCompletedTasks(): void
    {
        self::assertSame(
            [
                [
                    'id' => 'completed-task',
                    'title' => 'Completed task',
                    'priority' => 'high',
                    'due_date' => '2026-09-14',
                    'description' => 'Done already',
                    'completed' => true,
                ],
            ],
            TaskFilter::apply($this->tasks(), 'completed')
        );
    }

    public function testUnknownFilterFallsBackToAll(): void
    {
        $tasks = $this->tasks();

        self::assertSame('all', TaskFilter::normalize('missing'));
        self::assertSame($tasks, TaskFilter::apply($tasks, 'missing'));
    }

    public function testMalformedFilterInputFallsBackToAll(): void
    {
        self::assertSame('all', TaskFilter::normalize(['active']));
    }

    public function testFilteringDoesNotMutateTaskData(): void
    {
        $tasks = $this->tasks();
        $tasksBeforeFilter = $tasks;

        TaskFilter::apply($tasks, 'completed');

        self::assertSame($tasksBeforeFilter, $tasks);
    }

    private function tasks(): array
    {
        return [
            [
                'id' => 'active-task',
                'title' => 'Active task',
                'priority' => 'medium',
                'due_date' => null,
                'description' => null,
                'completed' => false,
            ],
            [
                'id' => 'completed-task',
                'title' => 'Completed task',
                'priority' => 'high',
                'due_date' => '2026-09-14',
                'description' => 'Done already',
                'completed' => true,
            ],
        ];
    }
}
