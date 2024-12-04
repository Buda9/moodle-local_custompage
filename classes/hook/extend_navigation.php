<?php
namespace local_custompage\hook;

use local_custompage\local\models\page as page_persistent;
use local_custompage\local\helpers\audience as audience_helper;

defined('MOODLE_INTERNAL') || die();

class extend_navigation implements \core\hook\described_hook {
    private \navigation_node $navigation;

    public function __construct(\navigation_node $navigation) {
        $this->navigation = $navigation;
    }

    public static function get_hook_description(): string {
        return 'Extends navigation with custom pages.';
    }

    public static function get_hook_tags(): array {
        return ['core', 'navigation'];
    }

    public function execute(): void {
        global $PAGE, $CFG, $USER;

        $CFG->dbunmodifiedcustommenuitems = $CFG->custommenuitems;

        $userid = $this->get_user_id();
        if (!$userid) {
            return;
        }

        $pages = audience_helper::user_pages_list($userid);
        $this->add_pages_to_navigation($pages);
    }

    private function get_user_id(): ?int {
        global $USER, $CFG;
        
        if (isloggedin()) {
            return (int)$USER->id;
        } 
        if ($CFG->guestloginbutton) {
            $guest = guest_user();
            return (int)$guest->id;
        }
        return null;
    }

    private function add_pages_to_navigation(array $pages): void {
        global $PAGE, $CFG;

        foreach ($pages as $pageid) {
            $custompage = page_persistent::get_record(['id' => (int)$pageid]);
            $this->add_page_to_menu($custompage);
            $this->add_page_to_navigation_node($custompage);
        }
    }

    private function add_page_to_menu(page_persistent $custompage): void {
        global $CFG;
        $pagename = $custompage->get_formatted_name();
        $pagetitle = $custompage->get_formatted_title() ?: $pagename;
        $pageid = $custompage->get('id');
        
        $CFG->custommenuitems .= "\n" . "$pagetitle|/local/custompage/view.php?id=$pageid\n";
    }

    private function add_page_to_navigation_node(page_persistent $custompage): void {
        global $PAGE;
        $pageid = $custompage->get('id');
        $frontpagenode = $this->get_or_create_frontpage_node();

        if ($PAGE->context->contextlevel == CONTEXT_CUSTOMPAGE && $pageid == $PAGE->context->instanceid) {
            $custompagenode = $frontpagenode->add(
                $custompage->get_formatted_name(),
                new \moodle_url('/local/custompage/view.php', ['id' => $pageid])
            );
            $custompagenode->make_active();
        } else {
            $frontpagenode->add(
                $custompage->get_formatted_name(),
                new \moodle_url('/local/custompage/view.php', ['id' => $pageid])
            );
        }
    }

    private function get_or_create_frontpage_node(): \navigation_node {
        global $PAGE;
        $frontpagenode = $this->navigation->find('home', null);
        if (!$frontpagenode) {
            $frontpagenode = $this->navigation->add(
                get_string('home'),
                new \moodle_url('/index.php'),
                \navigation_node::TYPE_ROOTNODE,
                null
            );
            $frontpagenode->force_open();
        }

        if ($PAGE->context->contextlevel != CONTEXT_CUSTOMPAGE) {
            $pagefrontnode = $PAGE->navigation->find('home', null);
            if (!$pagefrontnode) {
                $pagefrontnode = $PAGE->navigation->add(
                    get_string('home'),
                    new \moodle_url('/index.php'),
                    \navigation_node::TYPE_ROOTNODE,
                    null
                );
                $pagefrontnode->force_open();
            }
            return $pagefrontnode;
        }

        return $frontpagenode;
    }
}