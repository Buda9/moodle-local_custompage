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

namespace local_custompage\event;

use coding_exception;
use core\event\base;
use local_custompage\local\models\audience;
use moodle_url;

/**
 * Custom page deleted event class.
 *
 * @package     local_custompage
 * @copyright   2024 BitAscii Solutions <bitascii.dev@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @property-read array $other {
 *      Extra information about the event.
 *
 *      - int    pageid:      The id of the page
 * }
 */
class audience_deleted extends base {
    /**
     * Initialise the event data.
     */
    protected function init() {
        $this->data['objecttable'] = audience::TABLE;
        $this->data['crud'] = 'd';
        $this->data['edulevel'] = self::LEVEL_OTHER;
    }

    /**
     * Creates an instance from a custompage audience object
     *
     * @param audience $audience
     * @return self
     */
    public static function create_from_object(audience $audience): self {
        $eventparams = [
            'context'  => $audience->get_page()->get_context(),
            'objectid' => $audience->get('id'),
            'other' => [
                'pageid' => $audience->get('pageid'),
            ],
        ];
        $event = self::create($eventparams);
        $event->add_record_snapshot($event->objecttable, $audience->to_record());
        return $event;
    }

    /**
     * Returns localised general event name.
     *
     * @return string
     */
    public static function get_name() {
        return get_string('audiencedeletedevent', 'core_reportbuilder');
    }

    /**
     * Returns non-localised description of what happened.
     *
     * @return string
     */
    public function get_description() {
        $pageid = $this->other['pageid'];
        return "The user with id '$this->userid' deleted the audience with id '$this->objectid' in the custom page" .
            " with id '$pageid'.";
    }

    /**
     * Custom validations.
     *
     * @throws coding_exception
     */
    protected function validate_data(): void {
        parent::validate_data();
        if (!isset($this->objectid)) {
            throw new coding_exception('The \'objectid\' must be set.');
        }
        if (!isset($this->other['pageid'])) {
            throw new coding_exception('The \'pageid\' must be set in other.');
        }
    }

    /**
     * Returns relevant URL.
     *
     * @return moodle_url
     */
    public function get_url(): moodle_url {
        return new moodle_url('/local/custompage/edit.php', ['id' => $this->other['pageid']], 'audience');
    }
}
