# Repository instruction entry point

1. Read [README.md](../README.md) for product context and commands.
2. Read root [AGENTS.md](../AGENTS.md) for repository-wide rules.
3. Read the nearest nested `AGENTS.md` before editing inside its directory tree, if present.
4. Use relevant skills under `.agents/skills/`, especially [run-tests](../.agents/skills/run-tests/SKILL.md) for local testing.

Root `AGENTS.md` applies repository-wide. Nested `AGENTS.md` files supplement it within their directory tree; the closest applicable repository instruction wins on conflicts. Explicit user instructions take precedence, subject to safety boundaries.
