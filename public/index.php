<?php

declare(strict_types=1);

require dirname(__DIR__) . '/vendor/autoload.php';

use MiniAgentLab\TaskRepository;

$repository = new TaskRepository(
    dirname(__DIR__) . '/data/tasks.json'
);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $repository->add(
                $_POST['title'] ?? '',
                $_POST['priority'] ?? 'medium',
                $_POST['due_date'] ?? null,
                $_POST['description'] ?? null
            );
        }

        if ($action === 'toggle') {
            $repository->toggle($_POST['id'] ?? '');
        }

        if ($action === 'delete') {
            $repository->delete($_POST['id'] ?? '');
        }

        if ($action === 'edit') {
            $repository->updateTitle(
                $_POST['id'] ?? '',
                $_POST['title'] ?? ''
            );
        }

        header('Location: /');
        exit;
    } catch (Throwable $exception) {
        $error = $exception->getMessage();
    }
}

$tasks = $repository->all();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mini Agent Lab</title>
    <link rel="stylesheet" href="/styles.css">
</head>

<body>

    <main class="app">
        <header class="app-header">
            <h1>Mini Agent Lab</h1>
        </header>

        <?php if ($error !== null): ?>
            <p class="error-message">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="post" class="task-form">
            <input type="hidden" name="action" value="add">

            <label class="field field-title">
                <span>Task</span>
                <input
                    type="text"
                    name="title"
                    placeholder="New task"
                    required>
            </label>

            <label class="field">
                <span>Priority</span>
                <select name="priority">
                    <option value="low">Low</option>
                    <option value="medium" selected>Medium</option>
                    <option value="high">High</option>
                </select>
            </label>

            <label class="field">
                <span>Due date</span>
                <input
                    type="date"
                    name="due_date">
            </label>

            <label class="field field-description">
                <span>Description</span>
                <textarea
                    name="description"
                    placeholder="Description"></textarea>
            </label>

            <button type="submit" class="button button-primary">
                Add task
            </button>
        </form>

        <section class="task-list" aria-label="Tasks">
            <?php foreach ($tasks as $task): ?>
                <?php
                $priorityClass = match ($task['priority']) {
                    'low' => 'priority-low',
                    'high' => 'priority-high',
                    default => 'priority-medium',
                };
                ?>

                <article class="task-card<?= $task['completed'] ? ' task-card-completed' : '' ?>">
                    <form method="post" class="task-main">
                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($task['id']) ?>">

                        <button
                            type="submit"
                            name="action"
                            value="toggle"
                            class="button button-toggle">
                            <?= $task['completed'] ? 'Mark active' : 'Mark complete' ?>
                        </button>

                        <div class="task-content">
                            <div class="task-meta">
                                <span class="priority-badge <?= $priorityClass ?>">
                                    <?= htmlspecialchars(ucfirst($task['priority'])) ?>
                                </span>

                                <?php if ($task['due_date'] !== null): ?>
                                    <span class="due-date">
                                        Due <?= htmlspecialchars($task['due_date']) ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <h2>
                                <?= htmlspecialchars($task['title']) ?>
                            </h2>

                            <?php if ($task['description'] !== null): ?>
                                <p class="task-description">
                                    <?= htmlspecialchars($task['description']) ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <button
                            type="submit"
                            name="action"
                            value="delete"
                            class="button button-danger">
                            Delete
                        </button>
                    </form>

                    <form method="post" class="task-edit">
                        <input
                            type="hidden"
                            name="id"
                            value="<?= htmlspecialchars($task['id']) ?>">

                        <label class="field">
                            <span>Edit title</span>
                            <input
                                type="text"
                                name="title"
                                value="<?= htmlspecialchars($task['title']) ?>"
                                required>
                        </label>

                        <button
                            type="submit"
                            name="action"
                            value="edit"
                            class="button button-secondary">
                            Edit
                        </button>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>
    </main>

</body>

</html>
