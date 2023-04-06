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

namespace local_course\output;

use local_course\util\course;
use local_course\util\enrolment;
use renderable;
use templatable;
use renderer_base;

/**
 * Index renderable class.
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */
class index implements renderable, templatable {
    protected $context;
    protected $course;

    public function __construct($context, $course) {
        $this->context = $context;
        $this->course = $course;
    }

    public function export_for_template(renderer_base $output) {
        $courselistelement = new \core_course_list_element($this->course);

        $coursesupport = new course($courselistelement);

        $customfields = $coursesupport->get_custom_fields();
        $teacher = $coursesupport->get_teacher();

        $enrolement = new enrolment($courselistelement);
        $enrolementinstances = $enrolement->get_enrolment_instances();

        $context = [
            'courseid' => $this->course->id,
            'coursename' => $courselistelement->get_formatted_fullname(),
            'courseimage' => $coursesupport->get_courseimage(),
            'categoryname' => $coursesupport->get_category_name(),
            'headerimage' => $coursesupport->get_headerimage(),
            'hascustomfields' => (bool)count($customfields),
            'customfields' => $customfields,
            'teacher' => $teacher,
            'syllabus' => $coursesupport->get_syllabus(),
            'enrolbuttons' => $output->render_from_template('local_course/enrol_buttons', $enrolementinstances)
        ];

        return $context;
    }
}
