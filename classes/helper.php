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
 * Helper class
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_questionpopup;
defined('MOODLE_INTERNAL') || die;

/**
 * Class helper
 *
 * @package   block_questionpopup
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 */
class helper {

    /**
     * We are in DEBUG mode display more info than general.
     *
     * @return bool
     */
    public static function has_debugging_enabled() {
        global $CFG;

        // Check if the environment has debugging enabled.
        return ($CFG->debug >= 32767 && $CFG->debugdisplay == 1);
    }

    /**
     * user_has_answered_question
     *
     * @param int $contextid
     *
     * @return bool
     * @throws \dml_exception
     */
    public static function user_has_answered_question(int $contextid) : bool {
        global $DB, $USER, $SESSION;

        if (isset($SESSION->questionpopup[$contextid])) {
            return true;
        }

        // Don't show popup if there is no question connected.
        if ($DB->record_exists('block_questionpopup_answer', [
                'contextid' => $contextid,
                'userid' => $USER->id,
            ]) === false) {
            $SESSION->questionpopup[$contextid] = true;

            return true;
        }

        return false;
    }

    /**
     * get question for current local
     *
     * @param int $contextid
     *
     * @return string
     * @throws \dml_exception
     * @throws \coding_exception
     */
    public static function get_question(int $contextid) : string {

        global $DB;
        $record = $DB->get_record('block_questionpopup', [
            'contextid' => $contextid,
        ]);

        if ($record) {
            $question = (array)unserialize($record->question);
            $currentlanguage = current_language();

            return $question['question_' . $currentlanguage] ?? get_string('error:no_question_configured',
                    'block_questionpopup');
        }

        return get_string('error:no_question_configured', 'block_questionpopup');
    }
}