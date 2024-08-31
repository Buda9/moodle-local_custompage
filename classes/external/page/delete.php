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

namespace local_custompage\external\page;

use external_api;
use external_function_parameters;
use external_value;
use local_custompage\factories\page_factory;
use local_custompage\manager;
use local_custompage\permission;

defined('MOODLE_INTERNAL') || die();

global $CFG;
require_once("{$CFG->libdir}/externallib.php");

/**
 * External method for deleting a page
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class delete extends external_api {
    /**
     * External function to delete a page.
     *
     * @param int $pageid
     * @return bool
     */
    public static function execute(int $pageid): bool {
        [
        'pageid' => $pageid
        ] = self::validate_parameters(self::execute_parameters(), [
        'pageid' => $pageid,
        ]);

        $pagepersistent = manager::get_page_from_id($pageid);

        self::validate_context($pagepersistent->get_context());
        permission::require_can_edit_page($pagepersistent);

        $custompage = page_factory::create($pageid);

        return $custompage->delete();
    }

    /**
     * Describes the parameters for delete_page.
     *
     * @return external_function_parameters
     */
    public static function execute_parameters(): external_function_parameters {
        return new external_function_parameters(
            [
            'pageid' => new external_value(PARAM_INT, 'Page id'),
            ]
        );
    }

    /**
     * Describes the data returned from the external function.
     *
     * @return external_value
     */
    public static function execute_returns(): external_value {
        return new external_value(PARAM_BOOL, '', VALUE_REQUIRED);
    }
}
