# Task Edit

## Goal

Allow users to edit an existing task title.

## Prompt Used

Reconstructed prompt:

```text
Add task title editing to the task application.

Requirements:
- Add a way to edit the title of an existing task from the UI.
- Validate edited titles the same way new task titles are validated.
- Reject empty edited titles.
- Editing an unknown task id should not change existing tasks.
- Preserve completion status and existing task fields such as priority and due date.
- Add or update relevant PHPUnit tests.
- Run the complete test suite.
```

## Agent Workflow Used

Single implementation workflow. The task was implemented in the repository with title update behavior and tests.

## Branch Name

`feature/task-edit`

## Outcome

Task titles can now be edited. Empty titles are rejected, unknown ids are ignored without changing data, and editing preserves other task fields.
