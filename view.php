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
 *  view.php description here.
 *
 * @package    local_custompage
 * @copyright  2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

declare(strict_types=1);

use local_custompage\manager;
use local_custompage\permission;
use local_custompage\custom_context\context_custompage;

require_once('../../config.php');
require_once($CFG->dirroot . '/lib/adminlib.php');

$pageid = required_param('id', PARAM_INT);
$context = context_custompage::instance($pageid);

require_login(null, true);

$page = manager::get_page_from_id($pageid);
permission::require_can_view_page($page);

$pageurl = new moodle_url('/local/custompage/view.php', ['id' => $pageid]);
$title = get_string('pluginname', 'local_custompage');

$PAGE->set_context($context);
$PAGE->set_subpage($pageid);
$PAGE->set_pagelayout('report');
$PAGE->blocks->add_region('content');
$PAGE->set_title($page->get('title'));
$PAGE->set_url($pageurl);


/** @var \local_custompage\output\renderer $renderer */
$renderer = $PAGE->get_renderer('local_custompage');
$showfullpageeditorheader = false;

if ($PAGE->user_is_editing() && permission::can_edit_page($page)) {
    $showfullpageeditorheader = true;
}

echo $OUTPUT->header();

echo $OUTPUT->addblockbutton('content');

if ($showfullpageeditorheader) {
    echo $renderer->render_fullpage_editor_header($page);
}

echo $OUTPUT->custom_block_region('content');

echo $OUTPUT->footer();
