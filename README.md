Export plugin for Craft CMS
=================

Plugin that allows you to export data to CSV files.

Features:
- Export Entries and Entry Types
- Export Users and User Groups
- Export Categories
- Sortable export field order
- Has a hook "registerExportSource" to add/replace exports with your own source.

Todo:
- Export all ElementTypes (currently only Entries, Users and Categories)
- Support JSON and XML output
- Permissions, who can export what

Important:
The plugin's folder should be named "export"

Changelog
=================
###0.3.2###
- Allow multiple title columns when exporting multiple entry types

###0.3.1###
- Added the ability to export all entrytypes in a section at once

###0.3.0###
- Added the ability to export Categories
- Added the ability to sort the export field order

###0.2.4###
- Added the ability to export id's

###0.2.3###
- Fixed wrong parsing of Lightswitch values
- Fixed skipping of existing columns with NULL values

###0.2.2###
- Mostly common bugfixes and improvements

###0.2.1###
- Fixed a bug that would compromise exportSource data when multiple hooks were used

###0.2###
- Added a "registerExportSource" hook, so you can replace/add/delete export data from your own plugin

###0.1###
- Initial push to GitHub