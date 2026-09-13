<?php

declare(strict_types=1);

namespace MiniAgentLab;

final class TaskRepository
{
    private const DEFAULT_PRIORITY = 'medium';

    private const PRIORITIES = ['low', 'medium', 'high'];

    public function __construct(
        private readonly string $filePath
    ) {
    }

    public function all(): array
    {
        if (!file_exists($this->filePath)) {
            return [];
        }

        $content = file_get_contents($this->filePath);

        if ($content === false || trim($content) === '') {
            return [];
        }

        $tasks = json_decode($content, true);

        if (!is_array($tasks)) {
            return [];
        }

        foreach ($tasks as &$task) {
            if (is_array($task) && !array_key_exists('priority', $task)) {
                $task['priority'] = self::DEFAULT_PRIORITY;
            }
        }

        unset($task);

        return $tasks;
    }

    public function add(string $title, string $priority = self::DEFAULT_PRIORITY): void
    {
        $title = trim($title);
        $priority = trim($priority);

        if ($title === '') {
            throw new \InvalidArgumentException('Task title cannot be empty.');
        }

        if (!in_array($priority, self::PRIORITIES, true)) {
            throw new \InvalidArgumentException('Task priority must be low, medium, or high.');
        }

        $tasks = $this->all();

        $tasks[] = [
            'id' => bin2hex(random_bytes(8)),
            'title' => $title,
            'priority' => $priority,
            'completed' => false,
        ];

        $this->save($tasks);
    }

    public function toggle(string $id): void
    {
        $tasks = $this->all();

        foreach ($tasks as &$task) {
            if ($task['id'] === $id) {
                $task['completed'] = !$task['completed'];
                break;
            }
        }

        unset($task);

        $this->save($tasks);
    }

    private function save(array $tasks): void
    {
        $json = json_encode(
            $tasks,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
        );

        if ($json === false) {
            throw new \RuntimeException('Unable to encode tasks.');
        }

        if (file_put_contents($this->filePath, $json, LOCK_EX) === false) {
            throw new \RuntimeException('Unable to save tasks.');
        }
    }
}
