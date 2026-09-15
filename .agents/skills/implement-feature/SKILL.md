---
name: implement-feature
description: Implement a non-trivial feature in this repository using the planner, developer, and reviewer subagents. Use when a feature requires analysis, implementation, tests, and independent review. Do not use for analysis-only requests or trivial one-line changes.
---

# Feature implementation workflow

Follow the repository instructions in `AGENTS.md`.

## 1. Plan

Delegate the task to the custom `planner` subagent.

The planner must:

- inspect the relevant existing code;
- describe the current behavior;
- identify affected files;
- identify risks and edge cases;
- identify required tests;
- produce an implementation plan.

Wait for the planner to finish before continuing.

## 2. Implement

Pass the task requirements and the planner's implementation plan to the custom `developer` subagent.

The developer must:

- implement the smallest appropriate change;
- preserve unrelated existing behavior;
- add or update relevant tests;
- run the complete PHPUnit test suite;
- inspect the resulting diff.

Wait for the developer to finish.

## 3. Review

Delegate an independent review of the current uncommitted changes to the custom `reviewer` subagent.

The reviewer must not modify files.

The reviewer should check:

- functional correctness;
- regressions;
- validation and edge cases;
- test coverage;
- unnecessary changes.

## 4. Handle findings

If the reviewer reports meaningful findings:

- send the findings back to the developer;
- ask the developer to correct them;
- rerun the complete test suite;
- request one fresh review.

Do not continue indefinitely. Stop after one correction round and report remaining findings to the user.

## 5. Final report

Report:

- planner summary;
- files changed;
- test results;
- reviewer findings;
- corrections performed, if any;
- final approval status.

Do not commit, push, merge, or change branches unless explicitly requested by the user.