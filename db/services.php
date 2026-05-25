<?php
defined('MOODLE_INTERNAL') || die();

$functions = [
    'local_moodlecourseloader_update_section' => [
        'classname'     => 'local_moodlecourseloader\external\update_section',
        'methodname'    => 'execute',
        'description'   => 'Updates a course section name and/or summary HTML.',
        'type'          => 'write',
        'capabilities'  => 'moodle/course:update',
        'loginrequired' => true,
        'ajax'          => false,
    ],
    'local_moodlecourseloader_create_page' => [
        'classname'     => 'local_moodlecourseloader\external\create_page',
        'methodname'    => 'execute',
        'description'   => 'Creates a mod_page resource in the given course section.',
        'type'          => 'write',
        'capabilities'  => 'moodle/course:manageactivities',
        'loginrequired' => true,
        'ajax'          => false,
    ],
];

$services = [
    'Moodle Course Loader Service' => [
        'functions'       => [
            'core_competency_duplicate_competency_framework',
            'core_competency_duplicate_template',
            'core_course_create_categories',
            'core_course_create_courses',
            'core_course_delete_courses',
            'core_course_duplicate_course',
            'core_course_get_categories',
            'core_course_get_contents',
            'core_course_get_courses_by_field',
            'core_course_update_courses',
            'core_files_get_files',
            'core_files_upload',
            'core_webservice_get_site_info',
            'enrol_manual_enrol_users',
            'enrol_manual_unenrol_users',
            'local_moodlecourseloader_update_section',
            'local_moodlecourseloader_create_page',
        ],
        'restrictedusers' => 0,
        'enabled'         => 1,
        'shortname'       => 'moodlecourseloader',
    ],
];
