## [1.1.0] - 2026-04-21

### Added
- **Hierarchical Album System** - Multi-level albums with parent/child relationships
- **Visual Album Tree** - Indented dropdown showing full album hierarchy (📁 root, └─ sub-albums)
- **Sub-album Creation** - Parent album selector when creating new albums
- **Category Selection** - Choose categories when creating new albums (Music/Theater groups)
- **Reusable Album Selector Component** - DRY implementation shared across all upload forms
- **Reusable Upload Form Component** - Centralized upload logic for single/multiple/zip uploads
- **Complete Internationalization** - All upload components fully translated (RU, EN, EO)

### Changed
- Refactored upload components to use shared album-selector and upload-form components
- Improved visual hierarchy in album dropdowns with tree lines and icons
- Moved category selection to album creation level (not per-photo)

### Fixed
- Multi-level album indentation display in dropdowns
- Translation key consistency across all upload components

## [1.1.0] - 2026-04-19

### Added
- Single photo upload with metadata
- Multiple photos upload with drag-and-drop
- Albums index page with public and photographer views
- Status/approval system for albums and photos
- Status history tracking with comments
- Grid and list view modes for albums
- Album search and sort functionality
- Photographer statistics dashboard
- Unsorted album card for photographers
- Publish/Unpublish controls for photographers
- Status badges (pending, approved, published, rejected, blocked)

### Changed
- Albums route now public with conditional photographer tools
- Factories and seeders updated for status system
- ImageProcessingService optimized for Intervention Image v4

### Fixed
- Drag-and-drop functionality for file uploads
- Image preview generation
- EXIF data extraction
