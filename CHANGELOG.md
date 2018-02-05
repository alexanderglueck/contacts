# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## Unreleased
### Added
- Add authentication scaffold
- Add contact management basics
- Add some tests
- Add TravisCI
- Add StyleCI
- Add login logging
- Add two-factor auth
- Add log viewer
- Add `date_of_birth` field (#3)
- Add map with available addresses on the contact addresses detail view
- Add notification settings

### Changed
- Move `User` class into `Models` directory
- Replace `env()` calls in views with `config()` calls

### Fixed
- Fix dates stored on a leap year not being shown on non leap years (#1)

## 0.0.1 - 2018-01-16
### Added
 - Initial commit
