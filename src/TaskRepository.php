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
            if (!is_array($task)) {
                continue;
            }

            if (!array_key_exists('priority', $task)) {
                $task['priority'] = self::DEFAULT_PRIORITY;
            }

            if (!array_key_exists('due_date', $task) || $task['due_date'] === '') {
                $task['due_date'] = null;
            }
        }

        unset($task);

        return $tasks;
    }

    public function add(
        string $title,
        string $priority = self::DEFAULT_PRIORITY,
        ?string $dueDate = null
    ): void {
        $title = trim($title);
        $priority = trim($priority);

        if ($title === '') {
            throw new \InvalidArgumentException('Task title cannot be empty.');
        }

        if (!in_array($priority, self::PRIORITIES, true)) {
            throw new \InvalidArgumentException('Task priority must be low, medium, or high.');
        }

        $dueDate = $this->normalizeDueDate($dueDate);

        $tasks = $this->all();

        $tasks[] = [
            'id' => bin2hex(random_bytes(8)),
            'title' => $title,
            'priority' => $priority,
            'due_date' => $dueDate,
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

    public function delete(string $id): void
    {
        $tasks = [];

        foreach ($this->all() as $task) {
            if ($task['id'] !== $id) {
                $tasks[] = $task;
            }
        }

        $this->save($tasks);
    }

    public function updateTitle(string $id, string $title): void
    {
        $title = trim($title);

        if ($title === '') {
            throw new \InvalidArgumentException('Task title cannot be empty.');
        }

        $tasks = $this->all();

        foreach ($tasks as &$task) {
            if ($task['id'] === $id) {
                $task['title'] = $title;
                break;
            }
        }

        unset($task);

        $this->save($tasks);
    }

    private function normalizeDueDate(?string $dueDate): ?string
    {
        $dueDate = trim($dueDate ?? '');

        if ($dueDate === '') {
            return null;
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $dueDate) !== 1) {
            throw new \InvalidArgumentException('Task due date must be a valid YYYY-MM-DD date.');
        }

        [$year, $month, $day] = array_map('intval', explode('-', $dueDate));

        if (!checkdate($month, $day, $year)) {
            throw new \InvalidArgumentException('Task due date must be a valid YYYY-MM-DD date.');
        }

        return $dueDate;
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
