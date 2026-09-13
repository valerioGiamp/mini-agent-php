# Task Priority

## Goal

Add priority support to tasks so each task can be marked as low, medium, or high priority.

## Prompt Used

Reconstructed prompt:

```text
Add task priority support to the task application.

Requirements:
- Allow a user to choose a priority when creating a task.
- Supported priorities are low, medium, and high.
- Default new tasks to medium priority when no priority is provided.
- Preserve existing task behavior.
- Preserve compatibility with existing JSON task records that do not have a priority.
- Validate priority values.
- Add or update relevant PHPUnit tests.
- Run the complete test suite.
```

## Agent Workflow Used

Single implementation workflow. The task was implemented in the repository with application code and PHPUnit coverage updated.

## Branch Name

`feature/task-priority`

## Outcome

Tasks now include a `priority` field, existing tasks without priority are treated as medium priority, invalid priorities are rejected, and the UI exposes low, medium, and high choices.
