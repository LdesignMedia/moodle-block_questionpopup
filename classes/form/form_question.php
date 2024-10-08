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
 * Context question
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

namespace block_questionpopup\form;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->libdir . '/formslib.php');

/**
 * Class form_question
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 */
class form_question extends \moodleform {

    /**
     * Form definition. Abstract method - always override!
     */
    protected function definition() {
        $mform = &$this->_form;
        $locals = get_string_manager()->get_list_of_translations();

        array_walk($locals, [$this, 'add_local_question'], 1);
        array_walk($locals, [$this, 'add_local_question'], 2);
        array_walk($locals, [$this, 'add_local_question'], 3);

        $mform->addElement('header', 'header_maxlength', get_string('form:header_maxlength', 'block_questionpopup'));

        $array = range(0, 250);

        $mform->addElement('select', 'maxlength_question_1', '1 -' . get_string('form:maxlength_question', 'block_questionpopup'), $array);
        $mform->setType('maxlength_question_1', PARAM_INT);
        $mform->addElement('select', 'maxlength_question_2', '2 -' . get_string('form:maxlength_question', 'block_questionpopup'), $array);
        $mform->setType('maxlength_question_2', PARAM_INT);
        $mform->addElement('select', 'maxlength_question_3', '3 -' . get_string('form:maxlength_question', 'block_questionpopup'), $array);
        $mform->setType('maxlength_question_3', PARAM_INT);

        $this->add_action_buttons(true, get_string('btn:submit', 'block_questionpopup'));
    }

    /**
     * add_local_question
     *
     * @param string $localheading
     * @param string $local
     *
     * @param int $questionnumber
     *
     * @throws \coding_exception
     */
    public function add_local_question($localheading, $local, int $questionnumber = 0) {

        $mform = &$this->_form;
        $mform->addElement('header', 'header_' . $local, get_string('form:question', 'block_questionpopup') . ' - ' . $questionnumber . ' : ' . $localheading);

        $name = 'question_' . $questionnumber . '_' . $local;
        $mform->addElement('text', $name, get_string('form:question', 'block_questionpopup'), [
            'style' => 'width:100%',
        ]);
        $mform->setType($name, PARAM_TEXT);
    }

}
