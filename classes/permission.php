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

use context;
use context_system;
use local_custompage\local\helpers\audience;
use local_custompage\local\models\page;

/**
 * Page permission class
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class permission {
    /**
     * Require given user can view pages list
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @throws page_access_exception
     */
    public static function require_can_view_pages_list(?int $userid = null, ?context $context = null): void {
        if (!static::can_view_pages_list($userid, $context)) {
            throw new page_access_exception();
        }
    }

    /**
     * Whether given user can view pages list
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @return bool
     */
    public static function can_view_pages_list(?int $userid = null, ?context $context = null): bool {
        global $CFG;

        if ($context === null) {
            $context = context_system::instance();
        }

        return has_any_capability([
            'local/custompage:editall',
            'local/custompage:edit',
            'local/custompage:view',
        ], $context, $userid);
    }

    /**
     * Require given user can view page
     *
     * @param page $page
     * @param int|null $userid User ID to check, or the current user if omitted
     * @throws page_access_exception
     */
    public static function require_can_view_page(page $page, ?int $userid = null): void {
        if (!static::can_view_page($page, $userid)) {
            throw new page_access_exception('errorpageview');
        }
    }

    /**
     * Whether given user can view page
     *
     * @param page $page
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return bool
     */
    public static function can_view_page(page $page, ?int $userid = null): bool {
        if (static::can_view_pages_list($userid, $page->get_context())) {
            return true;
        }

        if (self::can_edit_page($page, $userid)) {
            return true;
        }

        $pages = audience::user_pages_list($userid);
        if (in_array($page->get('id'), $pages)) {
            return true;
        }

        return false;
    }

    /**
     * Require given user can edit page
     *
     * @param page $page
     * @param int|null $userid User ID to check, or the current user if omitted
     * @throws page_access_exception
     */
    public static function require_can_edit_page(page $page, ?int $userid = null): void {
        if (!static::can_edit_page($page, $userid)) {
            throw new page_access_exception('errorpageedit');
        }
    }

    /**
     * Whether given user can edit page
     *
     * @param page $page
     * @param int|null $userid User ID to check, or the current user if omitted
     * @return bool
     */
    public static function can_edit_page(page $page, ?int $userid = null): bool {
        global $CFG, $USER;

        // To edit their own pages, users must have either of the 'edit' or 'editall' capabilities. For pages
        // belonging
        // to other users, they must have the specific 'editall' capability.
        $userid = $userid ?: (int) $USER->id;
        if ($page->get('usercreated') === $userid) {
            return has_any_capability([
                'local/custompage:edit',
                'local/custompage:editall',
            ], $page->get_context(), $userid);
        } else {
            return has_capability('local/custompage:editall', $page->get_context(), $userid);
        }
    }

    /**
     * Whether given user can create a new page
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @return bool
     */
    public static function can_create_page(?int $userid = null, ?context $context = null): bool {
        return is_siteadmin($userid);
    }

    /**
     * Require given user can create a new page
     *
     * @param int|null $userid User ID to check, or the current user if omitted
     * @param context|null $context
     * @throws page_access_exception
     */
    public static function require_can_create_page(?int $userid = null, ?context $context = null): void {
        if (!static::can_create_page($userid, $context)) {
            throw new page_access_exception('errorpagecreate');
        }
    }
}
