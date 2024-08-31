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

namespace local_custompage;

use local_custompage\local\models\page;
use stdClass;

/**
 * Page management class
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class manager {
    /**
     * Return an instance of a page class from the given page ID
     *
     * @param int $pageid
     * @return page
     */
    public static function get_page_from_id(int $pageid): page {
        return new page($pageid);
    }

    /**
     * Create new page persistent
     *
     * @param stdClass $pagedata
     * @return page
     */
    public static function create_page_persistent(stdClass $pagedata): page {
        return (new page(0, $pagedata))->create();
    }
}
