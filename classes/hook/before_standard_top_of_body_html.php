<?php
namespace local_custompage\hook;

defined('MOODLE_INTERNAL') || die();

class before_standard_top_of_body_html implements \core\hook\described_hook {
    public static function get_hook_description(): string {
        return 'Triggered before standard top of body HTML is generated.';
    }

    public static function get_hook_tags(): array {
        return ['core', 'output'];
    }

    public function execute(): void {
        global $CFG;
        if (isset($CFG->dbunmodifiedcustommenuitems)) {
            $CFG->custommenuitems = $CFG->dbunmodifiedcustommenuitems;
            unset($CFG->dbunmodifiedcustommenuitems);
        }
    }
}