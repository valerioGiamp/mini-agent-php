<?php

declare(strict_types=1);

use MiniAgentLab\TaskRepository;
use PHPUnit\Framework\TestCase;

final class TaskRepositoryTest extends TestCase
{
    private string $filePath;

    protected function setUp(): void
    {
        $this->filePath = sys_get_temp_dir()
            . '/tasks-'
            . uniqid('', true)
            . '.json';

        file_put_contents($this->filePath, '[]');
    }

    protected function tearDown(): void
    {
        if (file_exists($this->filePath)) {
            unlink($this->filePath);
        }
    }

    public function testTaskCanBeAdded(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Learn Codex agents', 'high');

        $tasks = $repository->all();

        self::assertCount(1, $tasks);
        self::assertSame(
            'Learn Codex agents',
            $tasks[0]['title']
        );
        self::assertSame('high', $tasks[0]['priority']);
        self::assertFalse($tasks[0]['completed']);
    }

    public function testTaskDefaultsToMediumPriority(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Learn defaults');

        $tasks = $repository->all();

        self::assertSame('medium', $tasks[0]['priority']);
    }

    public function testExistingTaskWithoutPriorityUsesMediumPriority(): void
    {
        file_put_contents(
            $this->filePath,
            json_encode(
                [
                    [
                        'id' => 'task-id',
                        'title' => 'Existing task',
                        'completed' => false,
                    ],
                ],
                JSON_PRETTY_PRINT
            )
        );

        $repository = new TaskRepository($this->filePath);

        $tasks = $repository->all();

        self::assertSame('medium', $tasks[0]['priority']);
    }

    public function testInvalidPriorityCannotBeAdded(): void
    {
        $repository = new TaskRepository($this->filePath);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task priority must be low, medium, or high.');

        $repository->add('Invalid priority', 'urgent');
    }

    public function testTaskCanBeToggled(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Test task', 'low');

        $tasks = $repository->all();

        $repository->toggle($tasks[0]['id']);

        $tasks = $repository->all();

        self::assertTrue($tasks[0]['completed']);
        self::assertSame('low', $tasks[0]['priority']);
    }
}
