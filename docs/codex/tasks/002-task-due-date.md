# Task Due Date

## Goal

Add optional due date support to tasks.

## Prompt Used

Reconstructed prompt:

```text
Add due date support to the task application.

Requirements:
- Allow a user to set an optional due date when creating a task.
- Store due dates in YYYY-MM-DD format.
- Treat blank due dates as no due date.
- Preserve compatibility with existing JSON task records that do not have a due date.
- Validate due date format and reject invalid calendar dates.
- Preserve existing task behavior.
- Add or update relevant PHPUnit tests.
- Run the complete test suite.
```

## Agent Workflow Used

Single implementation workflow. The task was implemented in the repository with validation and PHPUnit coverage.

## Branch Name

`feature/task-due-date`

## Outcome

Tasks now support an optional `due_date` field. Blank or missing due dates are normalized to `null`, valid dates are stored as `YYYY-MM-DD`, and invalid dates are rejected.
