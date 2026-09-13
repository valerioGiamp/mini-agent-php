# Mini Agent PHP

## Project

This is a small PHP application used to practice agentic software development.

Technical stack:

- PHP 8.3
- Composer 2
- PHPUnit 10
- PSR-4 autoloading
- No framework
- JSON file persistence

## Project structure

- `src/` contains application classes.
- `public/` contains the web entry point.
- `data/` contains application data.
- `tests/` contains PHPUnit tests.

## Development rules

- Use strict types in PHP files.
- Follow the existing code style and project structure.
- Prefer small, focused changes.
- Do not introduce a framework.
- Do not add Composer dependencies unless they are necessary for the task.
- Do not modify unrelated files.
- Preserve existing behaviour unless the task explicitly requires changing it.

## Testing

Run the test suite with:

`vendor/bin/phpunit tests`

After changing application logic:

- add or update relevant tests;
- run the test suite;
- report any failing tests.

## Git

- Do not create commits unless explicitly requested.
- Do not push changes.
- Do not merge branches.
- Do not change branches unless explicitly requested.

## Working style

Before implementing a task:

1. inspect the relevant existing code;
2. identify the files likely to be affected;
3. explain the intended changes;
4. identify any ambiguity or risk.

When explicitly asked only to analyse a task, do not modify files.