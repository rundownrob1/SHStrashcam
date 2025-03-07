<?php
// This file is part of Moodle - http://moodle.org/

defined('MOODLE_INTERNAL') || die();

$capabilities = [
    // Capability to add the activity to a course.
    'mod/photoupload:addinstance' => [
        'riskbitmask' => 0,
        'captype'     => 'write',
        'contextlevel'=> CONTEXT_COURSE,
        'archetypes'  => [
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        ],
        'clonepermissionsfrom' => 'moodle/course:manageactivities'
    ],

    // Capability to view the activity (for students, teachers, etc.).
    'mod/photoupload:view' => [
        'captype'      => 'read',
        'contextlevel' => CONTEXT_MODULE,
        'archetypes'   => [
            'guest'           => CAP_PREVENT,
            'student'         => CAP_ALLOW,
            'teacher'         => CAP_ALLOW,
            'manager'         => CAP_ALLOW
        ]
    ],
];
