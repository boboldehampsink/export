Export plugin for Craft CMS
=================

Plugin that allows you to export data to CSV files.

Features:
 - Export Entries and Entry Types
 - Export Users and User Groups
 
Todo:
 - Export all ElementTypes (currently only Entries and Users)
 - Support JSON and XML output
 - Permissions, who can export what
 
Important:
The plugin's folder should be named "export"

Changelog
=================
###0.2###
 - Added a "registerExportSource" hook, so you can replace/add/delete export data from your own plugin

###0.1###
 - Initial push to GitHub