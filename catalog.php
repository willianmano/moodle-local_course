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
 * Course catalog page.
 *
 * @package     local_course
 * @copyright   2025 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

require(__DIR__ . '/../../config.php');

$search = optional_param('search', '', PARAM_TEXT);
$categoryid = optional_param('category', 0, PARAM_INT);

$context = \core\context\system::instance();

$url = new \moodle_url('/local/course/catalog.php', ['search' => $search, 'categoryid' => $categoryid]);

$PAGE->set_context($context);
$PAGE->set_url($url);
$PAGE->set_title(get_string('catalog', 'local_course'));
$PAGE->set_pagelayout('standard');

$renderer = $PAGE->get_renderer('local_course');

echo $renderer->header();

$page = new \local_course\output\catalog($search, $categoryid);

echo $renderer->render($page);

echo $OUTPUT->footer();
