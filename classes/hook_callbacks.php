<?php

namespace local_custompage;
use navigation_node;

defined('MOODLE_INTERNAL') || die();

class hook_callbacks {
    public static function before_standard_top_of_body_html(): void {
        $hook = new \local_custompage\hook\before_standard_top_of_body_html();
        $hook->execute();
    }

    public static function extend_navigation(\navigation_node $nav): void {
        $hook = new \local_custompage\hook\extend_navigation($nav);
        $hook->execute();
    }

    public static function after_config(): void {
        $hook = new \local_custompage\hook\after_config();
        $hook->execute();
    }
}