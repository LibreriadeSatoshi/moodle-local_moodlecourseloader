<?php
namespace local_moodlecourseloader\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

class update_section extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid'   => new external_value(PARAM_INT, 'Course ID'),
            'sectionnum' => new external_value(PARAM_INT, 'Section number (0-based)'),
            'name'       => new external_value(PARAM_TEXT, 'Section name', VALUE_DEFAULT, ''),
            'summary'    => new external_value(PARAM_RAW, 'Section summary HTML', VALUE_DEFAULT, ''),
        ]);
    }

    public static function execute(int $courseid, int $sectionnum, string $name = '', string $summary = ''): array {
        global $DB;

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid'   => $courseid,
            'sectionnum' => $sectionnum,
            'name'       => $name,
            'summary'    => $summary,
        ]);

        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        $context = \context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:update', $context);

        $section = $DB->get_record(
            'course_sections',
            ['course' => $course->id, 'section' => $params['sectionnum']]
        );
        if (!$section) {
            $section = course_create_section($course->id, $params['sectionnum']);
        }

        $data = [];
        if ($params['name'] !== '') {
            $data['name'] = $params['name'];
        }
        if ($params['summary'] !== '') {
            $data['summary'] = $params['summary'];
            $data['summaryformat'] = FORMAT_HTML;
        }

        if (!empty($data)) {
            course_update_section($course, $section, $data);
        }

        return ['success' => true];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'success' => new external_value(PARAM_BOOL, 'Whether the update succeeded'),
        ]);
    }
}
