<?php
namespace local_moodlecourseloader\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

class set_course_image extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid'  => new external_value(PARAM_INT, 'Course ID'),
            'filename'  => new external_value(PARAM_FILE, 'Image filename (e.g. cover.jpg)'),
            'imagedata' => new external_value(PARAM_RAW, 'Base64-encoded image content'),
            'mimetype'  => new external_value(PARAM_RAW, 'MIME type (e.g. image/jpeg)'),
        ]);
    }

    public static function execute(int $courseid, string $filename, string $imagedata, string $mimetype): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid'  => $courseid,
            'filename'  => $filename,
            'imagedata' => $imagedata,
            'mimetype'  => $mimetype,
        ]);

        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        $context = \context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);

        $content = base64_decode($params['imagedata'], true);
        if ($content === false) {
            throw new \moodle_exception('invalidbase64', 'local_moodlecourseloader');
        }

        $fs = get_file_storage();
        $fs->delete_area_files($context->id, 'course', 'overviewfiles', 0);

        $fileinfo = [
            'contextid' => $context->id,
            'component' => 'course',
            'filearea'  => 'overviewfiles',
            'itemid'    => 0,
            'filepath'  => '/',
            'filename'  => $params['filename'],
            'mimetype'  => $params['mimetype'],
        ];
        $fs->create_file_from_string($fileinfo, $content);

        rebuild_course_cache($course->id, true);

        return ['success' => true];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the image was saved successfully'),
        ]);
    }
}
