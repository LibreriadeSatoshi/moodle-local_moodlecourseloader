<?php
namespace local_moodlecourseloader\external;

use core_external\external_api;
use core_external\external_function_parameters;
use core_external\external_single_structure;
use core_external\external_value;

defined('MOODLE_INTERNAL') || die();

class create_page extends external_api {

    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters([
            'courseid'   => new external_value(PARAM_INT, 'Course ID'),
            'sectionnum' => new external_value(PARAM_INT, 'Target section number (0-based)'),
            'name'       => new external_value(PARAM_TEXT, 'Page name'),
            'content'    => new external_value(PARAM_RAW, 'Page body HTML'),
            'visible'    => new external_value(PARAM_INT, 'Visibility (1=visible, 0=hidden)', VALUE_DEFAULT, 1),
        ]);
    }

    public static function execute(int $courseid, int $sectionnum, string $name, string $content, int $visible = 1): array {
        global $DB, $CFG;

        require_once($CFG->dirroot . '/course/modlib.php');
        require_once($CFG->dirroot . '/mod/page/lib.php');

        $params = self::validate_parameters(self::execute_parameters(), [
            'courseid'   => $courseid,
            'sectionnum' => $sectionnum,
            'name'       => $name,
            'content'    => $content,
            'visible'    => $visible,
        ]);

        $course = $DB->get_record('course', ['id' => $params['courseid']], '*', MUST_EXIST);
        $context = \context_course::instance($course->id);
        self::validate_context($context);
        require_capability('moodle/course:manageactivities', $context);

        // Create the section if it does not exist yet.
        if (!$DB->record_exists('course_sections', ['course' => $course->id, 'section' => $params['sectionnum']])) {
            course_create_section($course->id, $params['sectionnum']);
        }

        $module = $DB->get_record('modules', ['name' => 'page'], '*', MUST_EXIST);

        $moduleinfo = new \stdClass();
        $moduleinfo->modulename     = 'page';
        $moduleinfo->module         = $module->id;
        $moduleinfo->course         = $course->id;
        $moduleinfo->section        = $params['sectionnum'];
        $moduleinfo->name           = $params['name'];
        $moduleinfo->visible        = $params['visible'];
        $moduleinfo->intro          = $params['content'];
        $moduleinfo->introformat    = FORMAT_HTML;
        $moduleinfo->content        = $params['content'];
        $moduleinfo->contentformat  = FORMAT_HTML;
        $moduleinfo->display        = 5; // RESOURCELIB_DISPLAY_OPEN
        $moduleinfo->printheading   = 1;
        $moduleinfo->printlastmodified = 1;
        $moduleinfo->cmidnumber     = '';
        $moduleinfo->groupmode      = 0;
        $moduleinfo->groupingid     = 0;
        $moduleinfo->availability   = null;
        $moduleinfo->completion     = 0;
        $moduleinfo->completionview = 0;

        $moduleinfo = add_moduleinfo($moduleinfo, $course);

        return ['cmid' => (int) $moduleinfo->coursemodule];
    }

    public static function execute_returns(): external_single_structure {
        return new external_single_structure([
            'cmid' => new external_value(PARAM_INT, 'Course module ID of the created page'),
        ]);
    }
}
