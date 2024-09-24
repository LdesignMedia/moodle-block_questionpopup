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
 * Webservice
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die;

/**
 * Class block_questionpopup_external
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 */
class block_questionpopup_external extends external_api {

    /**
     * If everything goes according plan, we can use this code.
     */
    const RESPONSE_CODE_SUCCESS = 200;

    /**
     * Save answer
     *
     * @param int $contextid
     * @param string $answer
     *
     * @return array
     */
    public static function save_answer(int $contextid, string $answer) {
        global $USER, $DB;
        $answerdata = [];
        parse_str($answer, $answerdata);
        require_capability('block/questionpopup:view', context::instance_by_id($contextid), $USER);

        $params = [
            'userid' => $USER->id,
            'contextid' => $contextid,
        ];

        $row = $DB->get_record('block_questionpopup_answer', $params);

        if ($row) {
            $DB->update_record('block_questionpopup_answer', (object) [
                'id' => $row->id,
                'answer' => serialize((object) $answerdata),
            ]);
        } else {

            $DB->insert_record('block_questionpopup_answer', (object) [
                'answer' => serialize((object) $answerdata),
                'userid' => $USER->id,
                'contextid' => $contextid,
                'created_at' => time(),
            ]);
        }

        return [
            'result_code' => self::RESPONSE_CODE_SUCCESS,
        ];
    }

    /**
     * save_answer_parameters
     *
     * @return external_function_parameters
     */
    public static function save_answer_parameters() {
        return new external_function_parameters (
            [
                'contextid' => new external_value(PARAM_INT, 'Context id', VALUE_REQUIRED),
                'answer' => new external_value(PARAM_RAW, 'The user there answer', VALUE_REQUIRED),
            ]
        );
    }

    /**
     * save_answer_returns
     *
     * @return external_single_structure
     */
    public static function save_answer_returns() {

        return new external_single_structure(
            [
                'result_code' => new external_value(PARAM_INT, 'The response code', VALUE_REQUIRED),
            ]);
    }

}
