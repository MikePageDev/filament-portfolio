# Changelog

All notable changes to this package are documented here. The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/), and this project uses [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-11-10

### Added

- The screenshot upload field lists the accepted formats and the 5 MB size limit.

### Changed

- Requires `illuminate/database` 13 or above, so the package no longer installs on older Laravel versions.
- Updated the README screenshots of the projects table and project form.
- Removed the front-end screenshots, which belong to `portfolio-frontend`.

### Security

- Screenshot uploads now accept only JPEG, PNG, WebP, AVIF and GIF. SVG files are rejected because they can contain embedded JavaScript.

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
