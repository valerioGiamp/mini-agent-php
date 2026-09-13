# Task Description

## Goal

Add optional descriptions to tasks.

## Prompt Used

Reconstructed prompt:

```text
Add task description support to the task application.

Requirements:
- Allow a user to enter an optional description when creating a task.
- Trim description input.
- Treat blank descriptions as no description.
- Preserve compatibility with existing JSON task records that do not have a description.
- Limit descriptions to 200 characters.
- Preserve existing task behavior, including priority, due date, delete, edit, and toggle behavior.
- Add or update relevant PHPUnit tests.
- Run the complete test suite.
```

## Agent Workflow Used

Single implementation workflow. The task was implemented in the repository with repository normalization, UI display, and PHPUnit coverage.

## Branch Name

`feature/task-description`

## Outcome

Tasks now support an optional `description` field. Blank or missing descriptions are normalized to `null`, non-blank descriptions are trimmed, and descriptions over 200 characters are rejected.
