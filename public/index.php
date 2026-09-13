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
                $_POST['due_date'] ?? null
            );
        }

        if ($action === 'toggle') {
            $repository->toggle($_POST['id'] ?? '');
        }

        if ($action === 'delete') {
            $repository->delete($_POST['id'] ?? '');
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
    <title>Mini Agent Lab</title>
</head>

<body>

    <h1>Mini Agent Lab</h1>

    <?php if ($error !== null): ?>
        <p>
            <?= htmlspecialchars($error) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <input type="hidden" name="action" value="add">

        <input
            type="text"
            name="title"
            placeholder="New task"
            required>

        <select name="priority">
            <option value="low">Low</option>
            <option value="medium" selected>Medium</option>
            <option value="high">High</option>
        </select>

        <input
            type="date"
            name="due_date">

        <button type="submit">
            Add
        </button>
    </form>

    <hr>

    <?php foreach ($tasks as $task): ?>

        <form method="post">
            <input
                type="hidden"
                name="id"
                value="<?= htmlspecialchars($task['id']) ?>">

            <button type="submit" name="action" value="toggle">
                <?= $task['completed'] ? '✓' : '○' ?>
            </button>

            [<?= htmlspecialchars(ucfirst($task['priority'])) ?>]
            <?= htmlspecialchars($task['title']) ?>
            <?php if ($task['due_date'] !== null): ?>
                (Due: <?= htmlspecialchars($task['due_date']) ?>)
            <?php endif; ?>

            <button type="submit" name="action" value="delete">
                Delete
            </button>
        </form>

    <?php endforeach; ?>

</body>

</html>
