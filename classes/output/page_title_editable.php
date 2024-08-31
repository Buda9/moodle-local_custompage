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

namespace local_custompage\output;

use core\output\inplace_editable;
use core_external;
use html_writer;
use local_custompage\local\models\page;
use local_custompage\permission;
use moodle_url;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once("{$CFG->libdir}/external/externallib.php");

/**
 * Page title editable component
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class page_title_editable extends inplace_editable {
    /**
     * Class constructor
     *
     * @param int $pageid
     * @param page|null $page The page persistent, note that in addition to id/name properties being present we also
     *      require the following to be correctly set in order to perform permission checks: contextid/usercreated
     * @throws \coding_exception
     * @throws \moodle_exception
     */
    public function __construct(int $pageid, ?page $page = null) {
        if ($page === null) {
            $page = new page($pageid);
        }

        $editable = permission::can_edit_page($page);

        $url = $editable
            ? new moodle_url('/local/custompage/edit.php', ['id' => $page->get('id')])
            : new moodle_url('/local/custompage/view.php', ['id' => $page->get('id')]);

        $displayvalue = html_writer::link($url, $page->get_formatted_title());

        parent::__construct(
            'local_custompage',
            'pagetitle',
            $page->get('id'),
            $editable,
            $displayvalue,
            $page->get('title'),
            get_string('editpagetitle', 'local_custompage')
        );
    }

    /**
     * Update page persistent and return self, called from inplace_editable callback
     *
     * @param int $pageid
     * @param string $value
     * @return self
     */
    public static function update(int $pageid, string $value): self {
        $page = new page($pageid);

        core_external::validate_context($page->get_context());
        permission::require_can_edit_page($page);

        $value = trim(clean_param($value, PARAM_TEXT));
        if ($value !== '') {
            $page
                ->set('title', $value)
                ->update();
        }

        return new self(0, $page);
    }
}
