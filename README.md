# local_moodlecourseloader

Moodle local plugin that exposes web service functions missing from the Moodle 5.0 standard API. Built to support the [moodle-course-loader](https://github.com/LibreriadeSatoshi/moodle-course-loader) CLI.

## Why

Moodle 5.0 does not provide standard web services for:

- Updating course section names and summary HTML (`core_course_edit_section` is deprecated and only supports hide/show/stealth)
- Creating `mod_page` resources programmatically
- Uploading a course overview image via a single API call

## Web Service Functions

| Function | Description |
|---|---|
| `local_moodlecourseloader_update_section` | Update section name and/or summary HTML. Creates the section if it does not exist. |
| `local_moodlecourseloader_create_page` | Create a `mod_page` resource in a section. Creates the section if it does not exist. |
| `local_moodlecourseloader_set_course_image` | Upload a course overview image (base64) directly to `course/overviewfiles`. |

All functions require `loginrequired = true` and work with external tokens (not AJAX-only).

## Installation

### Option A — Via Moodle web interface

1. Download `moodlecourseloader.zip` from the [latest release](https://github.com/LibreriadeSatoshi/moodle-local_moodlecourseloader/releases).
2. Go to **Site administration → Plugins → Install plugins** and upload the ZIP.
3. Follow the installation wizard.

### Option B — Manual (recommended for servers with direct filesystem access)

```bash
cp -r local/moodlecourseloader/ <moodle_root>/local/
```

Then go to **Site administration → Notifications** to run the database upgrade.

## Configuration

After installation, enable the web service token:

1. **Site administration → Advanced features** → Enable web services ✓
2. **Site administration → Plugins → Web services → Manage protocols** → Enable REST ✓
3. **Site administration → Plugins → Web services → Manage tokens** → Create a token for the `Moodle Course Loader Service`

Or via CLI:

```bash
php admin/cli/cfg.php --name=enablewebservices --set=1
php admin/cli/cfg.php --name=webserviceprotocols --set=rest
```

## Requirements

- Moodle 4.5+
- PHP 8.2+
- `mod_page` plugin enabled

## Function Reference

### `local_moodlecourseloader_update_section`

```
courseid   (int)     Course ID
sectionnum (int)     Section number (0-based)
name       (string)  Section name — optional
summary    (string)  Section summary HTML — optional
```

Returns `{ "success": true }`.

### `local_moodlecourseloader_create_page`

```
courseid   (int)     Course ID
sectionnum (int)     Target section number (0-based)
name       (string)  Page title
content    (string)  Page body HTML
visible    (int)     1 = visible, 0 = hidden (default: 1)
```

Returns `{ "cmid": <int> }`.

### `local_moodlecourseloader_set_course_image`

```
courseid   (int)     Course ID
filename   (string)  Image filename (e.g. cover.jpg)
imagedata  (string)  Base64-encoded image content
mimetype   (string)  MIME type (e.g. image/jpeg)
```

Returns `{ "success": true }`. Replaces any existing course overview image.

## License

GNU GPL v3 — see [LICENSE](LICENSE).
