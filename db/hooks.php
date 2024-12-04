<?php
defined('MOODLE_INTERNAL') || die();

$callbacks = [
    [
        'hook' => \core\hook\output\before_standard_top_of_body_html::class,
        'callback' => [\local_custompage\hook_callbacks::class, 'before_standard_top_of_body_html'],
        'priority' => 500,
    ],
    [
        'hook' => \core\hook\navigation\extend_navigation::class,
        'callback' => [\local_custompage\hook_callbacks::class, 'extend_navigation'],
        'priority' => 500,
    ],
    [
        'hook' => \core\hook\after_config::class,
        'callback' => [\local_custompage\hook_callbacks::class, 'after_config'],
        'priority' => 500,
    ],
];