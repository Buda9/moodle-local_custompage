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

/**
 *  settings.php description here.
 *
 * @package    local_custompage
 * @copyright  2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

use core_admin\local\externalpage\accesscallback;
use local_custompage\permission;

defined('MOODLE_INTERNAL') || die();

  $ADMIN->add(
      'appearance',
      new admin_category('custompages', new lang_string('custompages', 'local_custompage')),
      'themes'
  );

  $ADMIN->add(
      'custompages',
      new accesscallback(
          'managecustompages',
          get_string('managecustompages', 'local_custompage'),
          (new moodle_url('/local/custompage/index.php'))->out(),
          static function (accesscallback $accesscallback): bool {
            return permission::can_view_pages_list();
          }
      )
  );
