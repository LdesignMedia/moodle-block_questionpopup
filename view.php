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
 * Show question edit form
 *
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 * @package   block_questionpopup
 * @copyright 2019-07-21 Mfreak.nl | LdesignMedia.nl - Luuk Verhoeven
 * @author    Luuk Verhoeven
 **/
require_once('../../config.php');
defined('MOODLE_INTERNAL') || die;

$contextid = required_param('contextid', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$course = $DB->get_record('course', ['id' => $courseid]);
require_login($course);

$PAGE->set_url('/blocks/questionpopup/view.php', [
    'courseid' => $courseid,
    'contextid' => $contextid,
]);
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->fullname);

$form = new \block_questionpopup\form\form_question($PAGE->url);
$questionpopup = $DB->get_record('block_questionpopup', ['contextid' => $contextid]);

if ($questionpopup) {
    $form->set_data(unserialize($questionpopup->question));
}

if ($form->is_cancelled()) {
    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

if (($data = $form->get_data()) != false) {

    if ($questionpopup) {
        $DB->update_record('block_questionpopup', (object) [
            'id' => $questionpopup->id,
            'question' => serialize($data),
        ]);
        redirect(new moodle_url('/course/view.php', ['id' => $courseid]));

        return;
    }

    $DB->insert_record('block_questionpopup', (object) [
        'contextid' => $contextid,
        'question' => serialize($data),
        'created_at' => time(),
    ]);

    redirect(new moodle_url('/course/view.php', ['id' => $courseid]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('header:question', 'block_questionpopup'));

echo $form->render();

echo $OUTPUT->footer();
