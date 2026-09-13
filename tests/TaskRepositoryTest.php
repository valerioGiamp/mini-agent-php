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

        $repository->add('Learn Codex agents', 'high', '2026-09-13');

        $tasks = $repository->all();

        self::assertCount(1, $tasks);
        self::assertSame(
            'Learn Codex agents',
            $tasks[0]['title']
        );
        self::assertSame('high', $tasks[0]['priority']);
        self::assertSame('2026-09-13', $tasks[0]['due_date']);
        self::assertFalse($tasks[0]['completed']);
    }

    public function testTaskDefaultsToMediumPriority(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Learn defaults');

        $tasks = $repository->all();

        self::assertSame('medium', $tasks[0]['priority']);
        self::assertNull($tasks[0]['due_date']);
    }

    public function testTaskCanBeAddedWithBlankDueDate(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Learn optional dates', 'medium', '');

        $tasks = $repository->all();

        self::assertNull($tasks[0]['due_date']);
    }

    public function testExistingTaskWithoutPriorityOrDueDateUsesDefaults(): void
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
        self::assertNull($tasks[0]['due_date']);
    }

    public function testInvalidPriorityCannotBeAdded(): void
    {
        $repository = new TaskRepository($this->filePath);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task priority must be low, medium, or high.');

        $repository->add('Invalid priority', 'urgent');
    }

    public function testInvalidDueDateFormatCannotBeAdded(): void
    {
        $repository = new TaskRepository($this->filePath);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task due date must be a valid YYYY-MM-DD date.');

        $repository->add('Invalid due date format', 'medium', '09/13/2026');
    }

    public function testInvalidDueDateCannotBeAdded(): void
    {
        $repository = new TaskRepository($this->filePath);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Task due date must be a valid YYYY-MM-DD date.');

        $repository->add('Invalid due date', 'medium', '2026-02-30');
    }

    public function testTaskCanBeToggled(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Test task', 'low', '2026-09-14');

        $tasks = $repository->all();

        $repository->toggle($tasks[0]['id']);

        $tasks = $repository->all();

        self::assertTrue($tasks[0]['completed']);
        self::assertSame('low', $tasks[0]['priority']);
        self::assertSame('2026-09-14', $tasks[0]['due_date']);
    }

    public function testTaskTitleCanBeEdited(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Original title', 'high', '2026-09-15');

        $tasks = $repository->all();

        $repository->toggle($tasks[0]['id']);
        $repository->updateTitle($tasks[0]['id'], 'Updated title');

        $tasks = $repository->all();

        self::assertCount(1, $tasks);
        self::assertSame('Updated title', $tasks[0]['title']);
        self::assertSame('high', $tasks[0]['priority']);
        self::assertSame('2026-09-15', $tasks[0]['due_date']);
        self::assertTrue($tasks[0]['completed']);
    }

    public function testEmptyTaskTitleCannotBeEdited(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Original title');

        $tasks = $repository->all();

        try {
            $repository->updateTitle($tasks[0]['id'], '   ');
            self::fail('Expected empty edited task title to be rejected.');
        } catch (\InvalidArgumentException $exception) {
            self::assertSame(
                'Task title cannot be empty.',
                $exception->getMessage()
            );
        }

        $tasks = $repository->all();

        self::assertSame('Original title', $tasks[0]['title']);
    }

    public function testEditingUnknownTaskDoesNotChangeExistingTasks(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('First task', 'high', '2026-09-13');
        $repository->add('Second task', 'medium');

        $tasksBeforeEdit = $repository->all();

        $repository->updateTitle('unknown-task-id', 'Updated title');

        self::assertSame($tasksBeforeEdit, $repository->all());
    }

    public function testTaskCanBeDeleted(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('First task', 'high', '2026-09-13');
        $repository->add('Second task', 'low', '2026-09-14');

        $tasks = $repository->all();

        $repository->delete($tasks[0]['id']);

        $tasks = $repository->all();

        self::assertCount(1, $tasks);
        self::assertSame('Second task', $tasks[0]['title']);
        self::assertSame('low', $tasks[0]['priority']);
        self::assertSame('2026-09-14', $tasks[0]['due_date']);
        self::assertFalse($tasks[0]['completed']);
    }

    public function testDeletingUnknownTaskDoesNotChangeExistingTasks(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('First task', 'high', '2026-09-13');
        $repository->add('Second task', 'medium');

        $tasksBeforeDelete = $repository->all();

        $repository->delete('unknown-task-id');

        self::assertSame($tasksBeforeDelete, $repository->all());
    }
}
