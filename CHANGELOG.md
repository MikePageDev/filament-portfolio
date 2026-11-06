# Changelog

All notable changes to this package are documented here. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2026-11-06

### Added

- Filament plugin with a Projects resource.
- Project fields: title, slug, summary, problem, role, tech tags, case-study sections, screenshots with alt text, live and source URLs, published flag and sort order.
- Reorderable projects table with a published toggle.
- Old screenshots are deleted from disk when replaced or removed, and when a project is deleted.
- Configurable filesystem disk and directory for screenshots.
- `Project` model with `published` and `ordered` query scopes and a factory.

### Changed

- Minimum Filament version raised to 5.10, the oldest version the test suite passes on.
