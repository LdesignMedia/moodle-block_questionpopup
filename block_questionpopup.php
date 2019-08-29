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
 * Block instance
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/

defined('MOODLE_INTERNAL') || die;

/**
 * Class block_questionpopup
 *
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  26-10-2018 MFreak.nl
 */
class block_questionpopup extends block_base {

    /**
     * Set the initial properties for the block
     *
     * @throws coding_exception
     */
    public function init() {
        $this->title = get_string('pluginname', 'block_questionpopup');
    }

    /**
     * Are you going to allow multiple instances of each block?
     * If yes, then it is assumed that the block WILL USE per-instance configuration
     *
     * @return boolean
     */
    public function instance_allow_multiple() {
        return false;
    }

    /**
     * Is each block of this type going to have instance-specific configuration?
     * Normally, this setting is controlled by {@link instance_allow_multiple()}: if multiple
     * instances are allowed, then each will surely need its own configuration. However, in some
     * cases it may be necessary to provide instance configuration to blocks that do not want to
     * allow multiple instances. In that case, make this function return true.
     * I stress again that this makes a difference ONLY if {@link instance_allow_multiple()} returns false.
     *
     * @return boolean
     */
    public function instance_allow_config() {
        return false;
    }

    /**
     * Set the applicable formats for this block to all
     *
     * @return array
     */
    public function applicable_formats() {
        return ['all' => true];
    }

    /**
     * Specialization.
     *
     * Happens right after the initialisation is complete.
     *
     * @return void
     * @throws coding_exception
     */
    public function specialization() {
        if (empty($this->config->title)) {
            $this->title = get_string('pluginname', 'block_questionpopup');
        } else {
            $this->title = $this->config->title;
        }
    }

    /**
     * The content object.
     *
     * @return stdClass
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function get_content() {
        global $COURSE;

        if ($this->content !== null) {
            return $this->content;
        }

        if ((!isloggedin() ||
            isguestuser() ||
            !has_capability('block/questionpopup:view', $this->context))) {
            return (object)['text' => ''];
        }

        $this->show_popup();

        $html = '';
        if (has_capability('block/questionpopup:addinstance', $this->context)) {
            $html .= html_writer::link(new moodle_url('/blocks/questionpopup/view.php', [
                'contextid' => $this->context->id,
                'courseid' => $COURSE->id,
            ]), get_string('btn:edit_question', 'block_questionpopup'), [
                'class' => 'btn btn-primary',
            ]);
            $html .= html_writer::link('#', get_string('btn:preview_question', 'block_questionpopup'), [
                'class' => 'btn btn-primary preview-question mt-1',
            ]);
        }
        $this->content = (object)[
            'text' => $html,
            'footer' => '',
        ];

        return $this->content;
    }

    /**
     * Show question popup
     *
     * @throws dml_exception
     * @throws coding_exception
     */
    protected function show_popup() {
        global $PAGE, $DB, $USER;

        $answer = $DB->get_record('block_questionpopup_answer', [
            'userid' => $USER->id,
            'contextid' => $this->context->id,
        ]);

        $question = \block_questionpopup\helper::get_question($this->context->id);
        if(empty($question)){
            return;
        }

        $PAGE->requires->strings_for_js(['js:popup_title'], 'block_questionpopup');
        $PAGE->requires->js_call_amd('block_questionpopup/questionpopup', 'initialise', [
            [
                'debugjs' => \block_questionpopup\helper::has_debugging_enabled(),
                'contextid' => $this->context->id,
                'question' => $question,
                'answer' => $answer->answer ?? '',
                'display' => \block_questionpopup\helper::user_has_answered_question($this->context->id) ? false : true,
            ],
        ]);
    }

}