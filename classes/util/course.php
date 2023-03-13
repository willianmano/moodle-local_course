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
 * Enrolment support class.
 *
 * @package    local_course
 * @copyright  2020 Willian Mano <willianmano@conecti.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_course\util;

defined('MOODLE_INTERNAL') || die();

use moodle_url;
use core_course_list_element;
use core_course_category;

/**
 * Enrolment class.
 *
 * @copyright  2020 Willian Mano <willianmano@conecti.me>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class course {
    protected $course;
    protected $coursewithformatoptions;

    public function __construct(core_course_list_element $course) {
        global $CFG;

        $this->course = $course;

        require_once($CFG->dirroot . '/course/format/lib.php');

        $this->coursewithformatoptions = course_get_format($course->id)->get_course();
    }

    public function get_summary() {
        global $CFG;

        if ($this->course->has_summary()) {
            require_once($CFG->dirroot . '/course/renderer.php');

            $chelper = new \coursecat_helper();

            return $chelper->get_course_formatted_summary($this->course,
                ['overflowdiv' => true, 'noclean' => true, 'para' => false]);
        }

        return false;
    }

    public function get_courseimage() {
        global $CFG, $OUTPUT;

        foreach ($this->course->get_course_overviewfiles() as $file) {
            if ($file->is_valid_image()) {
                $url = moodle_url::make_file_url("$CFG->wwwroot/pluginfile.php",
                    '/' . $file->get_contextid() . '/' . $file->get_component() . '/' .
                    $file->get_filearea() . $file->get_filepath() . $file->get_filename(), !$file->is_valid_image());

                return $url->out();
            }
        }

        return $OUTPUT->get_generated_image_for_id($this->course->id);
    }

    public function get_headerimage() {
        global $CFG;

        if ($this->course->format != 'preview') {
            return $this->get_courseimage();
        }

        $file = format_preview_get_file('headerimage', $this->course->id, $this->coursewithformatoptions->headerimage);

        if (!$file) {
            return new moodle_url('/local/course/pix/bg2.png');
        }

        $url = "$CFG->wwwroot/pluginfile.php/" .
            $file->get_contextid()
            . "/" .
            $file->get_component()
            . "/" .
            $file->get_filearea()
            . "/" .
            $file->get_itemid()
            . "/" .
            $file->get_filename()
            . "?forcedownload=1";

        return $url;
    }

    public function get_custom_fields() {
        if ($this->course->has_custom_fields()) {
            $coursecustomfields = $this->course->get_custom_fields();

            foreach ($coursecustomfields as $data) {
                $fielddata = new \core_customfield\output\field_data($data);

                $value = $fielddata->get_value();
                $customfields[] = (object)[
                    'name' => $fielddata->get_name(),
                    'hasvalue' => ($value !== null),
                    'value' => $value
                ];
            }

            return $customfields;
        }

        return [];
    }

    public function get_category_name() {
        if ($category = core_course_category::get($this->course->category, IGNORE_MISSING)) {
            return $category->get_formatted_name();
        }

        return false;
    }

    /**
     * Get first teacher info.
     *
     * @return array
     *
     * @throws \coding_exception
     * @throws \dml_exception
     */
    public function get_teacher() {
        global $DB;

        if ($this->course->has_course_contacts()) {
            $instructors = $this->course->get_course_contacts();

            foreach ($instructors as $key => $instructor) {
                $user = $DB->get_record('user', ['id' => $key]);

                $userutil = new user();

                return [
                    'fullname' => $instructor['username'],
                    'image' => $userutil->get_user_picture($user, 200),
                    'description' => format_text($user->description, $user->descriptionformat)
                ];
            }
        }

        return [];
    }

    public function get_syllabus() {
        if ($this->course->format != 'preview') {
            return $this->get_summary();
        }

        $syllabus = \format_text($this->coursewithformatoptions->syllabus_editor['text'], $this->coursewithformatoptions->syllabus_editor['format']);

        if (!empty($syllabus)) {
            return $syllabus;
        }

        return $this->get_summary();
    }
}
