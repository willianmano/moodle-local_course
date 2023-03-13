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
 * Course preview page
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__.'/../../config.php');

$id = required_param('id', PARAM_INT);

$course = $DB->get_record('course', ['id' => $id], '*', MUST_EXIST);

$context = \context_course::instance($course->id);

$url = new moodle_url('/local/course/index.php', ['id' => $id]);

$PAGE->set_context($context);
if (has_capability('moodle/course:update', $context)) {
    $PAGE->set_course($course);
}
$PAGE->set_url($url);
$PAGE->set_title($course->fullname);
$PAGE->set_heading($course->fullname);

$renderer = $PAGE->get_renderer('local_course');

echo $renderer->header();

$page = new \local_course\output\index($context, $course);

echo $renderer->render($page);

echo $OUTPUT->footer();
