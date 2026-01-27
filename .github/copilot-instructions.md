# AI Coding Agent Instructions

## Iteration Workflow

1. **Analyze Skills Folder**: Always review `.github/skills/` for available skills and their usage.
2. **Use Planning Skill**: For any task, use the "planning-with-files" skill to create `task_plan.md`, `findings.md`, and `progress.md`.
3. **Gather Context**: Read key files (README.md, docs/, composer.json) to understand architecture.
4. **Explore Modules**: Check `modules/` for component-specific patterns (e.g., Openwire for Alpine.js, Vuewire for Vue.js).
5. **Validate Changes**: Run tests/builds after edits; use SQLite in-memory for fast validation.
6. **Iterate**: Update plans based on findings, ensure patches and configs are handled via composer.

## Key Patterns

- DB config via `MAHO_DB_*` in `.env`; install state in `core_config_data`.
- Reactive components: PHP blocks + JS runtimes, AJAX updates.
- Tests: Pest/PHPUnit with in-memory DB.