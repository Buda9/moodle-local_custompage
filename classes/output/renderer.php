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
 *  renderer.php description here.
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
declare(strict_types=1);
namespace local_custompage\output;
use html_writer;
use moodle_url;
use plugin_renderer_base;
use local_custompage\local\models\page;

/**
 * renderer for local_custompage
 */
class renderer extends plugin_renderer_base {
    /**
     * Renders the New page button
     *
     * @return string
     */
    public function render_new_page_button(): string {
        return html_writer::tag('button', get_string('newpage', 'local_custompage'), [
        'class' => 'btn btn-primary my-auto',
        'data-action' => 'page-create',
        ]);
    }

    /**
     * Renders full page editor header
     *
     * @param page $page
     * @return string
     */
    public function render_fullpage_editor_header(page $page): string {
        $pagename = $page->get_formatted_name();

        $closebutton = html_writer::link(
            new moodle_url('/local/custompage/edit.php', ['id' => $page->get('id')]),
            get_string('closebuttontitle'),
            [
            'class' => 'btn btn-secondary',
            'title' => get_string('closebuttontitle', 'moodle', $pagename),
            'role' => 'button',
            ]
        );
        $context = [
        'title' => $pagename,
        'closebutton' => $closebutton,
        'output' => $this->output,
        ];

        return $this->render_from_template('local_custompage/editor_navbar', $context);
    }

    /**
     * render page details
     * @param $moodlepage
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_page_deatils($moodlepage) {
        $data = $moodlepage->export_for_template($this);
        return $this->render_from_template('local_custompage/page_details', $data);
    }

    /**
     * render page contents
     * @param $moodlepage
     * @return bool|string
     * @throws \moodle_exception
     */
    public function render_page_contents($moodlepage) {
        $data = $moodlepage->export_for_template($this);
        return $this->render_from_template('local_custompage/page_contents', $data);
    }
}
