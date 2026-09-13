<?php

declare(strict_types=1);

namespace MiniAgentLab;

final class TaskRepository
{
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

        return is_array($tasks) ? $tasks : [];
    }

    public function add(string $title): void
    {
        $title = trim($title);

        if ($title === '') {
            throw new \InvalidArgumentException('Task title cannot be empty.');
        }

        $tasks = $this->all();

        $tasks[] = [
            'id' => bin2hex(random_bytes(8)),
            'title' => $title,
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