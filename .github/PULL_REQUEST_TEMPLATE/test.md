---
name: Test / QA Update
about: Pull Request template for adding or updating tests
title: "[TEST] "
labels: [testing, qa]
assignees: ""
---

## Description

<!-- Describe what tests are added or updated (unit, integration, e2e, etc.) -->

## Motivation

<!-- Why are these tests needed? (e.g., coverage improvement, regression prevention) -->

## Changes Made

<!-- List the tests added or updated -->
- 
- 
- 

## Related Issue / Ticket

<!-- Reference related issues if applicable, e.g., closes #123 -->

## Testing Steps / Verification

<!-- How to run the tests and verify they work -->

1. Run tests: `npm test` / `php artisan test` / etc.
2. Check coverage: `coverage report` / etc.
3. Validate expected outcomes

## Expected Result

<!-- Describe expected results, e.g., all tests pass, coverage >= 90% -->

## Checklist

- [ ] All new and existing tests pass
- [ ] Test coverage is sufficient
- [ ] Test code follows project conventions
- [ ] No production code broken by test updates
- [ ] Documentation for tests updated (if applicable)
