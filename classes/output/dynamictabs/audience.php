<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

declare(strict_types=1);

namespace local_custompage\output\dynamictabs;

use core\output\dynamic_tabs\base;
use local_custompage\external\custom_page_audience_cards_exporter;
use local_custompage\local\helpers\audience as audience_helper;
use local_custompage\local\models\page;
use local_custompage\output\audience_heading_editable;
use local_custompage\permission;
use renderer_base;

/**
 * Audience dynamic tab
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class audience extends base {
    /**
     * Export this for use in a mustache template context.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        // Get all the audiences types to populate the left menu.
        $menucardsexporter = new custom_page_audience_cards_exporter(null);
        $menucards = (array) $menucardsexporter->export($output);

        // Get all current audiences instances for this page.
        $audienceinstances = $this->get_all_page_audiences();

        $data = [
            'tabheading' => get_string('audience', 'core_reportbuilder'),
            'pageid' => $this->data['pageid'],
            'contextid' => (new page((int)$this->data['pageid']))->get('contextid'),
            'sidebarmenucards' => $menucards,
            'instances' => $audienceinstances,
            'hasinstances' => !empty($audienceinstances),
        ];

        return $data;
    }

    /**
     * The label to be displayed on the tab
     *
     * @return string
     */
    public function get_tab_label(): string {
        return get_string('audience', 'core_reportbuilder');
    }

    /**
     * Check permission of the current user to access this tab
     *
     * @return bool
     */
    public function is_available(): bool {
        $pagepersistent = new page((int)$this->data['pageid']);
        return permission::can_edit_page($pagepersistent);
    }

    /**
     * Template to use to display tab contents
     *
     * @return string
     */
    public function get_template(): string {
        return 'local_custompage/local/dynamictabs/audience';
    }

    /**
     * Get all current audiences instances for this page.
     *
     * @return array
     */
    private function get_all_page_audiences(): array {
        global $PAGE;

        $renderer = $PAGE->get_renderer('core');

        $audienceinstances = [];
        $pageaudiences = audience_helper::get_base_records((int)$this->data['pageid']);
        $showormessage = false;
        foreach ($pageaudiences as $pageaudience) {
            $persistent = $pageaudience->get_persistent();
            $canedit = $pageaudience->user_can_edit();

            $editable = new audience_heading_editable(0, $persistent);

            $params = [
                'instanceid' => $persistent->get('id'),
                'description' => $pageaudience->get_description(),
                'heading' => $pageaudience->get_name(),
                'headingeditable' => $editable->render($renderer),
                'canedit' => $canedit,
                'candelete' => $canedit,
                'showormessage' => $showormessage,
            ];
            $audienceinstances[] = $params;
            $showormessage = true;
        }

        return $audienceinstances;
    }
}
