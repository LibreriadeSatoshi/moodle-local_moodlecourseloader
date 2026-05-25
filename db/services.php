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
            'local_moodlecourseloader_update_section',
            'local_moodlecourseloader_create_page',
        ],
        'restrictedusers' => 0,
        'enabled'         => 1,
        'shortname'       => 'moodlecourseloader',
    ],
];
