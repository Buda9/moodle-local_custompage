<?php
namespace local_custompage\hook;

defined('MOODLE_INTERNAL') || die();

class after_config implements \core\hook\described_hook {
    public static function get_hook_description(): string {
        return 'Adds custom context classes after config is loaded.';
    }

    public static function get_hook_tags(): array {
        return ['core', 'config'];
    }

    public function execute(): void {
        global $CFG;
        
        $customcontextclasses = [
            CONTEXT_CUSTOMPAGE => 'local_custompage\\custom_context\\context_custompage',
        ];

        if (isset($CFG->custom_context_classes)) {
            $CFG->custom_context_classes = array_merge(
                $CFG->custom_context_classes,
                $customcontextclasses
            );
        } else {
            $CFG->custom_context_classes = $customcontextclasses;
        }
    }
} 