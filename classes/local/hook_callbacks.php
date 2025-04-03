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
 * Event listener for dispatched event
 *
 * @package     local_course
 * @copyright   2023 Willian Mano {@link https://conecti.me}
 * @author      Willian Mano <willianmanoaraujo@gmail.com>
 */

namespace local_course\local;

use core\hook\output\before_standard_head_html_generation;

class hook_callbacks {
    /**
     * Callback to add head elements.
     *
     * @param \core\hook\output\before_standard_head_html_generation $hook
     */
    public static function before_standard_head_html_generation(
        \core\hook\output\before_standard_head_html_generation $hook,
    ): void {
        global $DB, $COURSE, $PAGE;

        $seocontent = '';

        $tagssql = 'SELECT t.id, t.name
                FROM {tag_instance} ti
                INNER JOIN {tag} t ON t.id = ti.tagid
                WHERE ti.itemtype = :itemtype AND itemid = :courseid AND contextid = :contextid';
        $params = [
            'itemtype' => 'course',
            'courseid' => $COURSE->id,
            'contextid' => $PAGE->context->id
        ];

        $coursetags = 'lms,ava,ead,ensinoadistancia';

        $dbcoursetags = $DB->get_records_sql($tagssql, $params);

        if ($dbcoursetags) {
            $tagsarr = [];
            foreach ($dbcoursetags as $tag) {
                $tagsarr[] = $tag->name;
            }

            $coursetags = implode(',', $tagsarr);
        }

        $courselistelement = new \core_course_list_element($COURSE);

        $coursesupport = new \local_course\util\course($courselistelement);

        $coursename = format_string($COURSE->fullname);

        $courseimg = $coursesupport->get_courseimage();

        $coursesummary = format_string($coursesupport->get_summary());

        // Geral.
        $seocontent .= "<meta name='description' content='{$coursesummary}'>";
        $seocontent .= "<meta name='keywords' content={$coursetags}'>";

        // Open graph.
        $seocontent .= "<meta property='og:locale' content='pt_br'>";
        $seocontent .= "<meta property='og:url' content='{$PAGE->url->out()}' />";
        $seocontent .= "<meta property='og:title' content='{$coursename}' />";
        $seocontent .= "<meta property='og:site_name' content='{$coursename}' />";
        $seocontent .= "<meta property='og:description' content='{$coursesummary}' />";
        $seocontent .= "<meta property='og:image' content='{$courseimg}' />";
        $seocontent .= "<meta property='og:image:type' content='image/jpeg'>";
        $seocontent .= "<meta property='og:image:width' content='1200'>";
        $seocontent .= "<meta property='og:image:height' content='630'>";
        $seocontent .= "<meta property='og:type' content='website'>";

        // Twitter.
        $seocontent .= "<meta name='twitter:title' content='{$coursename}' />";
        $seocontent .= "<meta name='twitter:card' content='summary_large_image' />";
        $seocontent .= "<meta name='twitter:description' content='{$coursesummary}' />";
        $seocontent .= "<meta name='twitter:image' content='{$courseimg}' />";

        $hook->add_html($seocontent);
    }
}