Export plugin for Craft CMS
=================

Plugin that allows you to export data to CSV files.

Features:
- Export Entries and Entry Types (All types or per type)
- Export Users and User Groups
- Export Categories
- Sortable export field order
- Renameable column labels
- Ability to save your sort and column settings
- Has a hook "registerExportSource" to add/replace exports with your own source.
- Has a hook "registerExportOperation" to parse special export fields
- Has a hook "registerExportService" to add your own Element Type export service.
- Has a hook "registerExportCsvDelimiter" to specify your preferred CSV delimiter.

Todo:
- Support JSON and XML output
- Handle large exports more smoothly

Important:
The plugin's folder should be named "export"

Changelog
=================
###0.5.7###
- Fetch elements individually for less memory consumption (thanks to @gijsstegehuis)

###0.5.6###
- Show spinner while generating export (thanks to @gijsstegehuis)
- Added the ability to register the preferred CSV delimiter via the registerExportCsvDelimiter hook

###0.5.5###
- Added ability to pre-select export elementtype through query string (thanks to @gijsstegehuis)
- Added Dutch translations (thanks to @rutgerbakker)

###0.5.4###
- Fixed export history maps (thanks to @MRolefes)

###0.5.3###
- Added the ability to control the sorting of data
- Improved the display of dates
- Improved the display of single option fields
- Use Windows friendly newlines

###0.5.2###
- Fixed multioptions fieldtype exporting

###0.5.1###
- Use native php csv export function to generate more correct csv's

###0.5.0###
- Added the ability to enter offset and limit for more control over the exported data and performance (#4)
- Fixed export map checkbox styling
- Added a registerExportService hook so you can write an export service for other/your own element type(s)
- Fixed escaping of slashes in export data
- Added MIT license

###0.4.8###
- Export now runs the export map differently through the element model for better export results, closing issues #2 and #3

###0.4.7###
- Added the ability to parse table checkboxes for export

###0.4.6###
- Added the ability to export Users's preferred locale, week start day, last login date, invalid login count and last invalid login date
- Clean up arrays before exporting, making them more readable

###0.4.5###
- Added getCsrfInput function to forms

###0.4.4###
- Even better field data parsing

###0.4.3###
- Better field data parsing

###0.4.2###
- Fixed a serious issue that led to not being able to run Export independently from Import

###0.4.1###
- Added a hook "registerExportOperation" to parse special export fields

###0.4.0###
- Added the ability to export parents and ancestors
- Added the ability to pick your own column names
- Added the ability to save column names and order
- You can also clear this (with permission to "reset")
- Fixed a lot of bugs with multiple title columns
- Only escape double quotes in CSV
- Ability to export fields that return an array

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