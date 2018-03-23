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
- Add `first_met` field to contact
- Add `note` field to contact
- Add `died_from` field to contact
- Add `died_at` field to contact
- Add image upload for the user
- Add (markdown) announcements
- Add `nationality_id` field to contact
- Add threaded comments to contacts
- Add option to delete account
- Add gift ideas to contact
- Add teams
  - A user can join multiple teams
  - Contacts/Contact Groups/Announcements are seperated by team

### Changed
- Move `User` class into `Models` directory
- Replace `env()` calls in views with `config()` calls
- Upgraded to Laravel 5.6
- Translated calendar
- Upgraded fullcalendar to the latest version (3.9)

### Fixed
- Fix dates stored on a leap year not being shown on non leap years (#1)
- Fixed bugsnag logging not working
- Fixed some down-migrations
- Fixed foreign key issues

## 0.0.1 - 2018-01-16
### Added
 - Initial commit
