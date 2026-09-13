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

        $repository->add('Learn Codex agents');

        $tasks = $repository->all();

        self::assertCount(1, $tasks);
        self::assertSame(
            'Learn Codex agents',
            $tasks[0]['title']
        );
        self::assertFalse($tasks[0]['completed']);
    }

    public function testTaskCanBeToggled(): void
    {
        $repository = new TaskRepository($this->filePath);

        $repository->add('Test task');

        $tasks = $repository->all();

        $repository->toggle($tasks[0]['id']);

        $tasks = $repository->all();

        self::assertTrue($tasks[0]['completed']);
    }
}